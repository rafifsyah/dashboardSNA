@extends('layouts.dashboard-wraper')

@push('dashboard-wraper.css')
<!-- Link CSS untuk D3.js jika diperlukan -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/d3/7.8.4/d3.min.css">
<style>
    th {
        cursor: pointer;
    }
    .label {
        font-size: 10px;
    }
</style>
@endpush

@push('dashboard-wraper.jscript')
<!-- Link ke D3.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/7.8.4/d3.min.js"></script>
@endpush

@section('dashboard-wraper.content')
    <div class="content mt-4">
        <div class="container-fluid">
            <div class="row px-2">
                <div class="col">
                    <!-- Dashboard Welcome Section -->
                    <div class="card card-secondary card-outline">
                        <div class="card-body p-5">
                            <div class="row">
                                <div class="col" style="max-width: 700px;">
                                    <h1 class="text-bold" style="color: #343A40;">Selamat Datang di</h1>
                                    <h1 class="text-bold text-secondary">Dashboard SOSIAL NETWORK ANALYSIS | GUDANG STEAK</h1>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- File Upload Form -->
                    <div class="card card-secondary card-outline">
                        <div class="card-body p-5">
                            <div class="row">
                                <div class="col" style="max-width: 700px;">
                                    <h1 class="text-bold" style="color: #343A40;">Form Upload File Followers dan Following</h1>
                                    <p class="text-bold text-secondary">Unggah file berformat JSON</p>
                                    @if (session('result') === 'success')
                                        <p>Files processed successfully. <a href="{{ url('storage/common_values.json') }}">Download common values</a></p>
                                    @endif

                                    @if ($errors->any())
                                        <div>
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <form action="{{ route('json.process') }}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        <label for="followers">Upload followers.json:</label>
                                        <input type="file" id="followers" name="followers" accept=".json" required>
                                        <br><br>
                                        <label for="following">Upload following.json:</label>
                                        <input type="file" id="following" name="following" accept=".json" required>
                                        <br><br>
                                        <input type="submit" value="Upload and Process">
                                    </form>



                                    <!-- Form untuk menjalankan skrip Python -->
                                    <button onclick="runPythonScript()">dapatkan jumlah psotingan, pengikut dan diikuti</button>

                                    <!-- Menampilkan log proses skrip Python -->
                                    <h2>aktifitas log pemerosesan</h2>
                                    <iframe id="log" src="" style="width: 100%; height: 400px; border: none;"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Graph Visualization and Table -->
                    <div class="card card-secondary card-outline">
                        <div class="card-body p-5">
                            <div class="row">
                                <div class="col">
                                    <h2>Graph Visualization</h2>
                                    <!-- Container untuk grafik -->
                                    <div id="graph-container" style="width: 100%; height: 800px;"></div>
                                    
                                    <h2>Centrality Measures</h2>
                                    <!-- Container untuk tabel -->
                                    <div id="table-container"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('dashboard-wraper.jscript')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Load graph and centrality data
        Promise.all([
            d3.json("/graph_output.json"),
            d3.json("/centrality_measures.json")
        ]).then(function([graph, centrality]) {
            const width = 1600;
            const height = 800;

            // Create SVG element for the graph
            const svg = d3.select("#graph-container").append("svg")
                .attr("width", width)
                .attr("height", height);

            // Set up force simulation
            const simulation = d3.forceSimulation(graph.nodes)
                .force("link", d3.forceLink(graph.links).id(d => d.id).distance(100))
                .force("charge", d3.forceManyBody().strength(-300))
                .force("center", d3.forceCenter(width / 2, height / 2))
                .force("x", d3.forceX().strength(0.1))
                .force("y", d3.forceY().strength(0.1))
                .on("tick", ticked);

            // Add links
            const link = svg.append("g")
                .attr("class", "links")
                .selectAll("line")
                .data(graph.links)
                .enter().append("line")
                .attr("class", "link")
                .style("stroke", "#999")
                .style("stroke-opacity", 0.6)
                .style("stroke-width", 1);

            // Add nodes
            const node = svg.append("g")
                .attr("class", "nodes")
                .selectAll("circle")
                .data(graph.nodes)
                .enter().append("circle")
                .attr("class", "node")
                .attr("r", 5)
                .on("mouseover", function(event, d) {
                    d3.select(this).attr("r", 8);
                    d3.select(`#label-${d.id}`).style("visibility", "visible");
                })
                .on("mouseout", function(event, d) {
                    d3.select(this).attr("r", 5);
                    d3.select(`#label-${d.id}`).style("visibility", "hidden");
                })
                .call(d3.drag()
                    .on("start", dragstarted)
                    .on("drag", dragged)
                    .on("end", dragended));

            // Add labels with links
            const label = svg.append("g")
                .attr("class", "labels")
                .selectAll("text")
                .data(graph.nodes)
                .enter().append("a")
                .attr("xlink:href", d => `https://www.instagram.com/${d.id}/`)
                .attr("target", "_blank")
                .append("text")
                .attr("class", "label")
                .attr("id", d => `label-${d.id}`)
                .text(d => d.id);

            function ticked() {
                link
                    .attr("x1", d => Math.max(0, Math.min(width, d.source.x)))
                    .attr("y1", d => Math.max(0, Math.min(height, d.source.y)))
                    .attr("x2", d => Math.max(0, Math.min(width, d.target.x)))
                    .attr("y2", d => Math.max(0, Math.min(height, d.target.y)));

                node
                    .attr("cx", d => Math.max(0, Math.min(width, d.x)))
                    .attr("cy", d => Math.max(0, Math.min(height, d.y)));

                label
                    .attr("x", d => Math.max(0, Math.min(width, d.x + 10)))
                    .attr("y", d => Math.max(0, Math.min(height, d.y + 3)));
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
            const nodesWithCentrality = graph.nodes.map(node => ({
                id: node.id,
                degree_centrality: (centrality.degree_centrality[node.id] || 0).toFixed(2),
                betweenness_centrality: (centrality.betweenness_centrality[node.id] || 0).toFixed(2),
                closeness_centrality: (centrality.closeness_centrality[node.id] || 0).toFixed(2),
                eigenvector_centrality: (centrality.eigenvector_centrality[node.id] || 0).toFixed(2)
            }));

            // Sort by a selected measure and get the top 10 nodes
            const measureToSortBy = 'degree_centrality';
            let sortedNodes = nodesWithCentrality.slice().sort((a, b) => b[measureToSortBy] - a[measureToSortBy]);
            let top10Nodes = sortedNodes.slice(0, 10);

            // Define columns for the table
            const columns = [
                { id: "id", name: "Node ID" },
                { id: "degree_centrality", name: "Degree Centrality" },
                { id: "betweenness_centrality", name: "Betweenness Centrality" },
                { id: "closeness_centrality", name: "Closeness Centrality" },
                { id: "eigenvector_centrality", name: "Eigenvector Centrality" }
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
                const headers = thead.append("tr")
                    .selectAll("th")
                    .data(columns)
                    .enter()
                    .append("th")
                    .text(d => d.name)
                    .on("click", function(event, d) {
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
                const rows = tbody.selectAll("tr")
                    .data(data)
                    .enter().append("tr");

                rows.selectAll("td")
                    .data(row => columns.map(column => ({
                        column: column.id,
                        value: column.id === 'id' ? `<a href="https://www.instagram.com/${row[column.id]}/" target="_blank">${row[column.id]}</a>` : row[column.id]
                    })))
                    .enter().append("td")
                    .html(d => d.value);
            }
        });

        // Dummy function for running Python script
        function runPythonScript() {
            alert("This function needs to be implemented.");
        }
    });
</script>
@endpush
