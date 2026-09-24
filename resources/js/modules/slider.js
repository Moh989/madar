const root=document.querySelector('[data-slider]');
if(root){
 const slides=[...root.querySelectorAll('[data-slide]')], dots=[...root.querySelectorAll('[data-dot]')];
 const reduced=matchMedia('(prefers-reduced-motion: reduce)');
 let current=0,timer=null,userPaused=reduced.matches,focused=false,startX=null,transitionId=0;
 const toggle=root.querySelector('[data-pause-toggle]');
 const imageReady=async(index)=>{const img=slides[index]?.querySelector('img');if(img?.dataset.src){img.src=img.dataset.src;delete img.dataset.src;try{await img.decode();}catch{}}};
 const clear=()=>{if(timer)clearInterval(timer);timer=null;};
 const paintPause=()=>{toggle.setAttribute('aria-label',userPaused?root.dataset.play:root.dataset.pause);toggle.setAttribute('aria-pressed',String(userPaused));toggle.querySelector('span').textContent=userPaused?'▷':'Ⅱ';};
 const schedule=()=>{clear();if(!userPaused&&!focused&&!document.hidden&&!reduced.matches)timer=setInterval(()=>show(current+1),6000);paintPause();};
 const show=async(index)=>{const id=++transitionId;index=(index+slides.length)%slides.length;await imageReady(index);if(id!==transitionId)return;slides[current].classList.remove('is-active');slides[current].setAttribute('aria-hidden','true');slides[current].inert=true;current=index;slides[current].classList.add('is-active');slides[current].setAttribute('aria-hidden','false');slides[current].inert=false;dots.forEach((dot,i)=>{dot.classList.toggle('active',i===current);dot.setAttribute('aria-pressed',String(i===current));});root.querySelector('[data-current]').textContent=String(current+1).padStart(2,'0');imageReady((current+1)%slides.length);schedule();};
 root.querySelector('[data-next]').addEventListener('click',()=>show(current+1));root.querySelector('[data-prev]').addEventListener('click',()=>show(current-1));dots.forEach((dot,i)=>dot.addEventListener('click',()=>show(i)));
 toggle.addEventListener('click',()=>{userPaused=!userPaused;schedule();});
 root.addEventListener('focusin',()=>{focused=true;clear();});root.addEventListener('focusout',()=>setTimeout(()=>{focused=root.contains(document.activeElement);schedule();},0));
 document.addEventListener('visibilitychange',schedule);reduced.addEventListener('change',()=>{userPaused=reduced.matches;schedule();});
 root.addEventListener('keydown',e=>{if(e.key==='ArrowLeft'||e.key==='ArrowRight'){e.preventDefault();const rtl=document.documentElement.dir==='rtl';show(current+((e.key==='ArrowLeft')===rtl?1:-1));}});
 root.addEventListener('touchstart',e=>{startX=e.changedTouches[0].clientX;},{passive:true});root.addEventListener('touchend',e=>{if(startX===null)return;const distance=e.changedTouches[0].clientX-startX;if(Math.abs(distance)>55)show(current+(distance<0?1:-1));startX=null;},{passive:true});
 imageReady(1);schedule();
}
