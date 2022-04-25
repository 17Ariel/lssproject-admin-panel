const openMenu=document.querySelector('#menu');
const closeMenu=document.querySelector('#sideclose');
const sidenav=document.querySelector('.sidenav');
const openNav=()=> document.querySelector('.sidenav').style.left="0";
const closeNav=()=>document.querySelector('.sidenav').style.left="-1000px";

openMenu.addEventListener('click',openNav);
closeMenu.addEventListener('click',closeNav);




