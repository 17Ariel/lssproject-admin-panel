const modal=document.querySelector('.modal-admin');
const confirms=document.querySelector('#admin');
const cancel= document.querySelector('#cancelIn');
const password=document.querySelector('#password');
const openAdmin=()=> modal.style.top="0";
const closeAdmin=()=> {
    modal.style.top="-2000px"
    password.value="";
};
const outsideClose=(e) =>{
    if(e.target==modal){
      e.preventDefault();
      modal.style.top='-3000px';
    }
  }
confirms.addEventListener('click',openAdmin);
cancel.addEventListener('click',closeAdmin);
window.addEventListener('click',outsideClose);


