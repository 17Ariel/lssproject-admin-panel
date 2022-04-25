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
    document.querySelector('#tdate').value="";
    document.querySelector('#name').value = "";
    document.querySelector('#contact').value = "";
    document.querySelector('#pname').value="";
    document.querySelector('#qty').value="";
    document.querySelector('#price').value="";
    document.querySelector('#reason').value="";
}
modal_open.addEventListener('click',openMod);
modal_close.addEventListener('click',closeMod);
window.addEventListener('click',closeModout);

