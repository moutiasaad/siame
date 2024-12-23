const name=document.getElementById('username')
const password=document.getElementById('password')
const form=document.getElementById('form') 
const error=document.getElementById('error') 
form.addEventListener('submit',(e)=> {
    
    let messages = []
    if(name.value===''||name.value==null){
    messages.push('login est vide')
}
    if(password.value.length<= 6){

       messages.push("le mot passe < 6 caractere")

    }
	if (messages.length>0){
    e.preventDefault()
	error.innerText=messages.join(', ')
	}
})
