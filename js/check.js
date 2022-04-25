const forms=document.querySelector('#formtransac');

const validatePayment=(e)=>{
    const total=document.querySelector('#totals').value;
    const payment=document.querySelector('#payments').value;
    if(parseFloat(total)>parseFloat(payment)){
        e.preventDefault();
        alert('Customer Payment is not enough');
        return false;
    }
    else{
        return true;
    }

}
forms.addEventListener('submit',validatePayment);