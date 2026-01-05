<div class="p-4">
    <div class="d-flex justify-content-between mb-5">
        <div class="shimmer rounded-3" style="width: 300px; height: 60px;"></div>
        <div class="shimmer rounded-pill" style="width: 200px; height: 50px;"></div>
    </div>

    <div class="glass-card rounded-5 bg-white overflow-hidden shadow-sm">
        <div class="p-4 border-bottom">
            <div class="shimmer rounded-pill" style="width: 40%; height: 45px;"></div>
        </div>
        <div class="p-5">
            @foreach(range(1, 5) as $i)
                <div class="d-flex align-items-center mb-4">
                    <div class="shimmer rounded-circle me-3" style="width: 50px; height: 50px;"></div>
                    <div class="w-100">
                        <div class="shimmer rounded-3 mb-2" style="width: 30%; height: 20px;"></div>
                        <div class="shimmer rounded-3" style="width: 20%; height: 15px;"></div>
                    </div>
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
            display: inline-block;
            position: relative;
            animation-duration: 1s;
            animation-fill-mode: forwards;
            animation-iteration-count: infinite;
            animation-name: shimmer;
            animation-timing-function: linear;
        }
        @keyframes shimmer {
            0% { background-position: -468px 0; }
            100% { background-position: 468px 0; }
        }
    </style>
</div>