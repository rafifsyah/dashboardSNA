@extends('layouts.dashboard-wraper')

@push('dashboard-wraper.css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/d3/7.8.4/d3.min.css">
<link rel="stylesheet" href="{{ asset('css/pages/dashboard.css') }}">
@endpush

@push('dashboard-wraper.jscript')
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
                                            <th>No</th>
                                            <th>Username</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>

                                <hr>
                                
                                <button onclick="runPythonScript('scrape.php')">Scrape jumlah post, followers, dan following</button>
                                
                                <hr>

                                <!-- Menampilkan tabel untuk data dari combined_following_data.json -->
                                <h2>jumlah post, Follower, Following</h2>
                                <table id="table_filtered" class="table table-bordered table-hover w-100">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Username</th>
                                            <th>Posts</th>
                                            <th>Followers</th>
                                            <th>Followees</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                                <hr>
                                
                                <h2>Scraping masing masing followers dari list filter</h2>
                                <button onclick="runPythonScript('run_node_graphy.php')">Scraping Follower</button>
                                
                                <hr>

                                <h2>Fungsi untuk mendapatkan node dan mendapatkan nilai Centrality Measures</h2>
                                <button onclick="runPythonScript('run_node_graphy.php')">Dapatkan Node Script</button>
                                <button onclick="runPythonScript('run_cm.php')">Hitung Centrality Measure</button>
                                
                                <hr>

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
