//add items variables
const adminModal=document.getElementsByClassName('modal')[0];
const modal_close=document.getElementById('closeMod');
const modal_open=document.getElementById('btnadd');


function openMod(){
  adminModal.style.top='0';
}
function closeMod(){
  adminModal.style.top='-3000px';
  clearIt();
}
function closeModout(e){
  if(e.target==adminModal){
    e.preventDefault();
    adminModal.style.top='-3000px';
    clearIt();
  }
}

const clearIt = () =>{
  document.querySelector('#bcode').value="";
  document.querySelector('#pname').value="";
  document.querySelector('#qtys').value="";
  document.querySelector('#prices').value="";
  document.querySelector('#expdate').value="";
}
modal_open.addEventListener('click',openMod);
modal_close.addEventListener('click',closeMod);
window.addEventListener('click',closeModout);

