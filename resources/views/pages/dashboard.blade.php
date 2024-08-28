@extends('layouts.dashboard-wraper')

@push('dashboard-wraper.css')
<!-- Link CSS untuk D3.js jika diperlukan -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/d3/7.8.4/d3.min.css">
<link rel="stylesheet" href="{{ asset('css/pages/dashboard.css') }}">
@endpush

@push('dashboard-wraper.jscript')
<!-- Link ke D3.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/7.8.4/d3.min.js"></script>
<script src="{{ asset('js/pages/dashboard.js') }}"></script>
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
                            <div class="col">
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

                                <form id="form_upload_json" onsubmit="uploadFollowersFollowing(this,event)">
                                    <label for="followers">Upload followers.json:</label>
                                    <input type="file" id="followers" name="followers" accept=".json" required>
                                    <br><br>
                                    <label for="following">Upload following.json:</label>
                                    <input type="file" id="following" name="following" accept=".json" required>
                                    <br><br>
                                    <input type="submit" value="Upload and Process">
                                </form>

                                <br>

                                <table id="table_common_values" class="table table-bordered table-hover w-100">
                                    <thead>
                                        <tr>
                                            <th>
                                                No
                                            </th>
                                            <th>
                                                Username
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>

                                <hr>

                                @if(!empty($filteredData))
                                    <h2>Filtered Following Data</h2>
                                    <form class="filter-form" method="get">
                                        <label for="min_followers">Min Followers:</label>
                                        <input type="number" id="min_followers" name="min_followers" value="{{ $minFollowers }}">
                                        <label for="max_followers">Max Followers:</label>
                                        <input type="number" id="max_followers" name="max_followers" value="{{ $maxFollowers }}">
                                        <br>
                                        <br>
                                        <label for="ensure_following_not_more_than_followers">Pastikan pengikut lebih banyak dibanding diikuti:</label>
                                        <input type="checkbox" id="ensure_following_not_more_than_followers" name="ensure_following_not_more_than_followers" {{ $ensureFollowingNotMoreThanFollowers ? 'checked' : '' }}>
                                        <input type="hidden" name="result" value="success">
                                        <input type="submit" value="Filter">
                                    </form>
                                    <table class="numbered-table">
                                        <tr><th>#</th><th>Username</th><th>Posts</th><th>Followers</th><th>Followees</th></tr>
                                        @foreach ($filteredData as $index => $entry)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td><a href="https://www.instagram.com/{{ htmlspecialchars($entry['username']) }}" target="_blank">{{ htmlspecialchars($entry['username']) }}</a></td>
                                                <td>{{ htmlspecialchars($entry['posts']) }}</td>
                                                <td>{{ htmlspecialchars($entry['followers']) }}</td>
                                                <td>{{ htmlspecialchars($entry['followees']) }}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                @else
                                    <p>No filtered following data found.</p>
                                @endif

                                <h2>Fungsi untuk mendapatkan node dan mendapatkan nilai Centrality Measures</h2>
                                <button onclick="runPythonScript('run_node_graphy.php')">Dapatkan Node Script</button>
                                <button onclick="runPythonScript('run_cm.php')">Hitung Centrality Measure</button>
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
