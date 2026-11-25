@if (session('status'))
    <div id="toast" class="show" role="status">{{ session('status') }}</div>
    <script>
        setTimeout(function () {
            var t = document.getElementById('toast');
            if (t) t.classList.remove('show');
        }, 1800);
    </script>
@endif
