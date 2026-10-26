document.querySelectorAll('[data-confirm]').forEach(x=>x.addEventListener('click',e=>{if(!confirm(x.dataset.confirm))e.preventDefault()}));
const preview=document.querySelector('#imagePreview'), file=document.querySelector('#image'); if(file&&preview){file.addEventListener('change',()=>{const f=file.files[0];if(f){preview.src=URL.createObjectURL(f);preview.style.display='block'}})}
