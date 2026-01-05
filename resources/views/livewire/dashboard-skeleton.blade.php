<div class="p-0 animate__animated animate__fadeIn">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div class="w-50">
            <div class="shimmer rounded-3 mb-2" style="width: 200px; height: 35px;"></div>
            <div class="shimmer rounded-3" style="width: 150px; height: 15px;"></div>
        </div>
        <div class="d-flex gap-2">
            <div class="shimmer rounded-pill" style="width: 100px; height: 45px;"></div>
            <div class="shimmer rounded-pill" style="width: 100px; height: 45px;"></div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        @foreach(range(1, 3) as $i)
        <div class="col-12 col-lg-4">
            <div class="card p-5 border-0 shadow-sm rounded-4 bg-white">
                <div class="shimmer rounded-3 mb-3" style="width: 40%; height: 20px;"></div>
                <div class="shimmer rounded-3" style="width: 80%; height: 40px;"></div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm rounded-5 overflow-hidden">
        <div class="p-4 border-bottom bg-white">
            <div class="shimmer rounded-pill" style="width: 30%; height: 30px;"></div>
        </div>
        <div class="p-4 bg-white">
            @foreach(range(1, 5) as $i)
            <div class="d-flex align-items-center mb-3">
                <div class="shimmer rounded-3 w-100" style="height: 50px;"></div>
            </div>
            @endforeach
        </div>
    </div>

    <style>
        .shimmer {
            background: #f6f7f8;
            background-image: linear-gradient(to right, #f6f7f8 0%, #edeef1 20%, #f6f7f8 40%, #f6f7f8 100%);
            background-repeat: no-repeat;
            background-size: 800px 100%; 
            animation: shimmer-ani 1.2s infinite linear;
        }
        @keyframes shimmer-ani {
            0% { background-position: -468px 0; }
            100% { background-position: 468px 0; }
        }
    </style>
</div>