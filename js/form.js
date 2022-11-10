
//function to get elements by ID
function element(id){
    return document.getElementById(id);
}

//define constants to use with the form
// const firstName = element('firstName');
// const lastName = element('lastName');
// const email = element('email');
// const password = element('password');
// const passwordConfirmation = element('passwordConfirmation');

form = element('form');
invalid = false;
const helper_default = '* Required';
const helper_default_pwd = '* Required. Must be minimum 8 characters. Must contain letters <b>and</b> numbers.';
const common_pwd = ['password1','qwerty123','password123'];


//checks if field empty and shows error messages on the field
function check_empty(id){
    var input = element(id);

    if ( input.value.trim() === '' ){ 
        let msg = element(id + "Help");
        msg.innerHTML = "This field cannot be empty";
        msg.classList.add("form-error");
        input.classList.add("form-error");
        invalid = true;
        
    };
};


//validate email address
function check_email(id){
    email_regex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;    
    var input = element(id);
    if( input.value.match(email_regex) == null ){
        let msg = element(id + "Help");
        msg.innerHTML = "Please enter a valid email address";
        msg.classList.add("form-error");
        input.classList.add("form-error");
        invalid = true;
    };   
};


//validate password
function check_password(id){   
    var input = element(id);

    if( input.value.length < 8 ){
        let msg = element(id + "Help");
        msg.innerHTML = "Password must be at least 8 characters";
        msg.classList.add("form-error");
        input.classList.add("form-error");
        invalid = true;
    }
    else if ( input.value.search(/[a-z]/i) < 0){
        let msg = element(id + "Help");
        msg.innerHTML = "Password must contain at least one letter";
        msg.classList.add("form-error");
        input.classList.add("form-error");
        invalid = true;
    }
    else if ( input.value.search(/[0-9]/i) < 0){
        let msg = element(id + "Help");
        msg.innerHTML = "Password must contain at least one digit";
        msg.classList.add("form-error");
        input.classList.add("form-error");
        invalid = true;
    }
    
    else if(common_pwd.includes(input.value.toLowerCase())){
        let msg = element(id + "Help");
        msg.innerHTML = "Please enter a stronger password";
        msg.classList.add("form-error");
        input.classList.add("form-error");
        invalid = true;

    };

    
};

//password match confirmation
function is_password_match(pwd, pwd_confirm){       
    var input = element(pwd);
    var match = element(pwd_confirm);
    if( input.value != match.value ){
        let msg = element(pwd_confirm + "Help");
        msg.innerHTML = "Passwords don't match";
        msg.classList.add("form-error");
        match.classList.add("form-error");
        invalid = true;
    };   
};



form.addEventListener('submit', function (e) {
    e.preventDefault();
    invalid = false;    
    
    //clear all previous errors. clean both css and field error messages to default values
    var errors = document.querySelectorAll('.form-error');
    if(errors.length > 0){
        errors.forEach( function(item) {
            item.classList.remove('form-error');                       
            if (item.tagName == 'SMALL'){                
                item.innerHTML = helper_default;};

            if (item.id == 'regPasswordHelp'){                
                item.innerHTML = helper_default_pwd;}
            });
        };      
    

    

        
    //check if fields empty
    check_empty('firstName');
    check_empty('lastName');
    check_empty('regEmail');
    check_empty('regPassword');
    check_email('regEmail');

    //check if password valid
    check_password('regPassword');  
    //check if passwords matching
    is_password_match('regPassword','regPasswordConfirmation');
    
    if (!invalid){
        form.submit();
    };
    
  }

);






