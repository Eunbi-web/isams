@php $savedTheme = auth()->user()->theme ?? 'system'; @endphp
<style>
/* ── Light/Dark theme switch ── */
.theme-switch{position:relative;width:58px;height:28px;flex-shrink:0;border-radius:20px;border:1.5px solid var(--bd,#cde0d0);background:var(--bg,#f2f7f3);cursor:pointer;padding:0;display:inline-flex;align-items:center;transition:background .25s,border-color .25s;}
.theme-switch:hover{border-color:var(--gm,#2d9e4f);}
.theme-switch .ts-ic{position:absolute;top:50%;transform:translateY(-50%);font-size:10px;z-index:2;transition:opacity .25s,color .25s;}
.theme-switch .ts-sun{left:9px;}
.theme-switch .ts-moon{right:9px;}
.theme-switch .ts-knob{position:absolute;top:2px;left:2px;width:20px;height:20px;border-radius:50%;z-index:1;background:linear-gradient(135deg,#f0c020,#c9a010);box-shadow:0 1px 4px rgba(0,0,0,.28);transition:transform .25s ease,background .25s;}
/* Light: knob rests on the sun (left) */
body[data-theme="light"] .theme-switch .ts-knob{transform:translateX(0);}
body[data-theme="light"] .theme-switch .ts-sun{color:#6b4a00;opacity:1;}
body[data-theme="light"] .theme-switch .ts-moon{color:var(--tm,#5a7a60);opacity:.45;}
/* Dark: knob slides onto the moon (right) */
body[data-theme="dark"] .theme-switch .ts-knob{transform:translateX(30px);background:linear-gradient(135deg,#4a6ea9,#22375f);}
body[data-theme="dark"] .theme-switch .ts-moon{color:#f5d07a;opacity:1;}
body[data-theme="dark"] .theme-switch .ts-sun{color:var(--tm,#8fa8c4);opacity:.45;}
</style>
<button type="button" id="themeToggleBtn" class="theme-switch" aria-label="Toggle light or dark mode" title="Toggle light / dark mode">
    <i class="fas fa-sun ts-ic ts-sun"></i>
    <i class="fas fa-moon ts-ic ts-moon"></i>
    <span class="ts-knob"></span>
</button>
<script>
(function(){
    var saved='{{ $savedTheme }}';
    var resolved=saved;
    if(saved==='system'){
        resolved=(window.matchMedia&&window.matchMedia('(prefers-color-scheme: dark)').matches)?'dark':'light';
    }
    document.body.setAttribute('data-theme',resolved);

    var btn=document.getElementById('themeToggleBtn');
    if(!btn) return;
    btn.addEventListener('click',function(){
        var next=(document.body.getAttribute('data-theme')==='dark')?'light':'dark';
        document.body.setAttribute('data-theme',next);
        var csrf=document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        fetch('{{ route('settings.theme.update') }}',{
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'X-Requested-With':'XMLHttpRequest'},
            body:JSON.stringify({theme:next})
        }).catch(function(){});
    });
})();
</script>
