document.addEventListener("DOMContentLoaded", function () {
    // Load graph and centrality data
    Promise.all([
        d3.json("/graph_output.json"),
        d3.json("/centrality_measures.json"),
    ]).then(function ([graph, centrality]) {
        const width = 1600;
        const height = 800;

        // Create SVG element for the graph
        const svg = d3
            .select("#graph-container")
            .append("svg")
            .attr("width", width)
            .attr("height", height);

        // Set up force simulation
        const simulation = d3
            .forceSimulation(graph.nodes)
            .force(
                "link",
                d3
                    .forceLink(graph.links)
                    .id((d) => d.id)
                    .distance(100)
            )
            .force("charge", d3.forceManyBody().strength(-300))
            .force("center", d3.forceCenter(width / 2, height / 2))
            .force("x", d3.forceX().strength(0.1))
            .force("y", d3.forceY().strength(0.1))
            .on("tick", ticked);

        // Add links
        const link = svg
            .append("g")
            .attr("class", "links")
            .selectAll("line")
            .data(graph.links)
            .enter()
            .append("line")
            .attr("class", "link")
            .style("stroke", "#999")
            .style("stroke-opacity", 0.6)
            .style("stroke-width", 1);

        // Add nodes
        const node = svg
            .append("g")
            .attr("class", "nodes")
            .selectAll("circle")
            .data(graph.nodes)
            .enter()
            .append("circle")
            .attr("class", "node")
            .attr("r", 5)
            .on("mouseover", function (event, d) {
                d3.select(this).attr("r", 8);
                d3.select(`#label-${d.id}`).style("visibility", "visible");
            })
            .on("mouseout", function (event, d) {
                d3.select(this).attr("r", 5);
                d3.select(`#label-${d.id}`).style("visibility", "hidden");
            })
            .call(
                d3
                    .drag()
                    .on("start", dragstarted)
                    .on("drag", dragged)
                    .on("end", dragended)
            );

        // Add labels with links
        const label = svg
            .append("g")
            .attr("class", "labels")
            .selectAll("text")
            .data(graph.nodes)
            .enter()
            .append("a")
            .attr("xlink:href", (d) => `https://www.instagram.com/${d.id}/`)
            .attr("target", "_blank")
            .append("text")
            .attr("class", "label")
            .attr("id", (d) => `label-${d.id}`)
            .text((d) => d.id);

        function ticked() {
            link.attr("x1", (d) => Math.max(0, Math.min(width, d.source.x)))
                .attr("y1", (d) => Math.max(0, Math.min(height, d.source.y)))
                .attr("x2", (d) => Math.max(0, Math.min(width, d.target.x)))
                .attr("y2", (d) => Math.max(0, Math.min(height, d.target.y)));

            node.attr("cx", (d) => Math.max(0, Math.min(width, d.x))).attr(
                "cy",
                (d) => Math.max(0, Math.min(height, d.y))
            );

            label
                .attr("x", (d) => Math.max(0, Math.min(width, d.x + 10)))
                .attr("y", (d) => Math.max(0, Math.min(height, d.y + 3)));
        }

        function dragstarted(event, d) {
            if (!event.active) simulation.alphaTarget(0.3).restart();
            d.fx = d.x;
            d.fy = d.y;
        }

        function dragged(event, d) {
            d.fx = event.x;
            d.fy = event.y;
        }

        function dragended(event, d) {
            if (!event.active) simulation.alphaTarget(0);
            d.fx = null;
            d.fy = null;
        }

        // Process and merge centrality data with node data
        const nodesWithCentrality = graph.nodes.map((node) => ({
            id: node.id,
            degree_centrality: (
                centrality.degree_centrality[node.id] || 0
            ).toFixed(2),
            betweenness_centrality: (
                centrality.betweenness_centrality[node.id] || 0
            ).toFixed(2),
            closeness_centrality: (
                centrality.closeness_centrality[node.id] || 0
            ).toFixed(2),
            eigenvector_centrality: (
                centrality.eigenvector_centrality[node.id] || 0
            ).toFixed(2),
        }));

        // Sort by a selected measure and get the top 10 nodes
        const measureToSortBy = "degree_centrality";
        let sortedNodes = nodesWithCentrality
            .slice()
            .sort((a, b) => b[measureToSortBy] - a[measureToSortBy]);
        let top10Nodes = sortedNodes.slice(0, 10);

        // Define columns for the table
        const columns = [
            { id: "id", name: "Node ID" },
            { id: "degree_centrality", name: "Degree Centrality" },
            { id: "betweenness_centrality", name: "Betweenness Centrality" },
            { id: "closeness_centrality", name: "Closeness Centrality" },
            { id: "eigenvector_centrality", name: "Eigenvector Centrality" },
        ];

        // Create the table
        createTable(top10Nodes, columns);

        function createTable(data, columns) {
            const tableContainer = d3.select("#table-container");

            // Remove existing table if any
            tableContainer.selectAll("table").remove();

            const table = tableContainer.append("table");
            const thead = table.append("thead");
            const tbody = table.append("tbody");

            // Create table headers with sorting functionality
            const headers = thead
                .append("tr")
                .selectAll("th")
                .data(columns)
                .enter()
                .append("th")
                .text((d) => d.name)
                .on("click", function (event, d) {
                    const ascending = d3.select(this).classed("ascending");
                    tbody.selectAll("tr").sort((a, b) => {
                        if (ascending) {
                            return d3.ascending(a[d.id], b[d.id]);
                        } else {
                            return d3.descending(a[d.id], b[d.id]);
                        }
                    });
                    // Toggle sort direction
                    d3.select(this).classed("ascending", !ascending);
                });

            // Add rows and cells to the table
            const rows = tbody.selectAll("tr").data(data).enter().append("tr");

            rows.selectAll("td")
                .data((row) =>
                    columns.map((column) => ({
                        column: column.id,
                        value:
                            column.id === "id"
                                ? `<a href="https://www.instagram.com/${
                                      row[column.id]
                                  }/" target="_blank">${row[column.id]}</a>`
                                : row[column.id],
                    }))
                )
                .enter()
                .append("td")
                .html((d) => d.value);
        }
    });

    // Dummy function for running Python script
    function runPythonScript(scriptName) {
        alert(
            "This function needs to be implemented with script: " + scriptName
        );
    }
});

$token = $.cookie("jwt_token");

function uploadFollowersFollowing(element, event) {
    event.preventDefault();
    showLoadingSpinner();

    let form = new FormData(element);

    $.ajax({
        type: "POST",
        url: `${BASE_URL}/api/v1/post-followers-following`,
        data: form,
        cache: false,
        processData: false,
        contentType: false,
        headers: {
            token: $token,
        },
        success: function (data) {
            hideLoadingSpinner();
            showToast("file berhasil <b>diupload!</b>", "success");
            getFollowersFollowing();
        },
        error: function (data) {
            hideLoadingSpinner();

            if (data.status >= 500) {
                showToast("kesalahan pada <b>server</b>", "danger");
            }
        },
    });
}
function uploadFiltered(element, event) {
    event.preventDefault();
    showLoadingSpinner();

    let form = new FormData(element);

    $.ajax({
        type: "POST",
        url: `${BASE_URL}/api/v1/get-filtered`,
        data: form,
        cache: false,
        processData: false,
        contentType: false,
        headers: {
            token: $token,
        },
        success: function (data) {
            hideLoadingSpinner();
            showToast("file berhasil <b>diupload!</b>", "success");
            getFollowersFollowing();
        },
        error: function (data) {
            hideLoadingSpinner();

            if (data.status >= 500) {
                showToast("kesalahan pada <b>server</b>", "danger");
            }
        },
    });
}

function getFollowersFollowing() {
    $.ajax({
        type: "GET",
        url: `${BASE_URL}/api/v1/get-followers-following`,
        headers: {
            token: $token,
        },
        success: function (arr_commons_value) {
            var newData = {}; 

            if (arr_commons_value) {
                newData = arr_commons_value.map(function (username, index) {
                    return {
                        no: index + 1,
                        username: username,
                    };
                });
            }

            $("#table_common_values").DataTable({
                data: newData,
                columns: [
                    {
                        data: "no",
                        width: "10%",
                        className: "text-center align-middle",
                    },
                    { data: "username", className: "align-middle" },
                ],
                responsive: true,
                autoWidth: false,
                pageLength: 10,
                order: [[0, "asc"]],
            });
        },
        error: function (data) {
            if (data.status >= 500) {
                showToast("kesalahan pada <b>server</b>", "danger");
            }
        },
    });
}
getFollowersFollowing()

function getFiltered() {
    $.ajax({
        type: "GET",
        url: `${BASE_URL}/api/v1/get-filtered`,
        headers: {
            token: $token,
        },
        success: function (data) { // data harus diakses dari respons
            $("#table_filtered").DataTable({
                data: data,
                columns: [
                    {
                        data: null, // Kolom nomor tidak ada di data JSON, ini akan dihitung secara dinamis
                        width: "10%",
                        className: "text-center align-middle",
                        render: function (data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {   
                        data: "username", 
                        className: "align-middle" 
                    },
                    {   
                        data: "posts", 
                        className: "align-middle" 
                    },
                    {   
                        data: "followers", 
                        className: "align-middle" 
                    },
                    {   
                        data: "followees", 
                        className: "align-middle" 
                    },
                ],
                responsive: true,
                autoWidth: false,
                pageLength: 10,
                order: [[0, "asc"]],
            });
        },
        error: function (xhr) {
            if (xhr.status >= 500) {
                showToast("kesalahan pada <b>server</b>", "danger");
            }
        },
    });
}


