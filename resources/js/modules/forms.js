const sector=document.querySelector('[data-sector-select]');const shipping=document.querySelector('[data-shipping-fields]');
if(sector&&shipping){const update=()=>{shipping.hidden=sector.selectedOptions[0]?.dataset.shipping!=='true';};sector.addEventListener('change',update);update();}
document.querySelectorAll('[data-confirm]').forEach(form=>form.addEventListener('submit',e=>{if(!confirm(form.dataset.confirm))e.preventDefault();}));
document.querySelectorAll('.inquiry-form').forEach(form=>form.addEventListener('submit',()=>{if(form.checkValidity())form.querySelector('[type=submit]').disabled=true;}));
