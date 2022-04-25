try{
    const closebtn=document.querySelector('#alert-closer');
    const closeMsg = () =>{
        document.querySelector('.alert').style.display="none";
    }
    closebtn.addEventListener('click',closeMsg);
}
catch(e){
    console.log('');
}


