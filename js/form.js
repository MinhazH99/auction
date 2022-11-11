
//function to get elements by ID
function element(id){
    return document.getElementById(id);
}


//gets the form element to be used later in the code
form = element('form');

//setting variables and constants that will be used later in the code
invalid = false;
const helper_default = '* Required';
const helper_default_pwd = '* Required. Must be minimum 8 characters. Must contain letters <b>and</b> numbers.';
const common_pwd = ['password1','qwerty123','password123'];

//checks if field empty and shows error messages on the field
function check_empty(id){
    var input = element(id);
    if ( input.value.trim() === '' ){ 
        field_error(id, "This field cannot be empty");    
    };
};

//if there are errors, shows them and applies error css
function field_error(id, error_msg){
        let msg = element(id + "Help");
        var input = element(id);
        msg.innerHTML = error_msg;
        msg.classList.add("form-error");
        input.classList.add("form-error");
        invalid = true;
};



//check name fields
function check_name(id){
    name_regex = /^[a-zA-Z-'.]+$/; // TODO adjust regex
    var input = element(id);
    if( input.value.match(name_regex) == null ){
        field_error(id, "Please enter a valid name");
    };
};

//validate email address
function check_email(id){
    email_regex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;    
    var input = element(id);
    if( input.value.match(email_regex) == null ){
        field_error(id, "Please enter a valid email address");      
    };   
};


//validate password
function check_password(id){   
    var input = element(id);

    if( input.value.length < 8 ){
        field_error(id, "Password must be at least 8 characters");        
    }
    else if ( input.value.search(/[a-z]/i) < 0){
        field_error(id, "Password must contain at least one letter");
    }
    else if ( input.value.search(/[0-9]/i) < 0){
        field_error(id, "Password must contain at least one digit");
    }
    
    else if(common_pwd.includes(input.value.toLowerCase())){
        field_error(id, "Please enter a stronger password");
    };

    
};

//password match confirmation
function is_password_match(pwd, pwd_confirm){       
    var input = element(pwd);
    var match = element(pwd_confirm);
    if( input.value != match.value ){
        field_error(pwd, "Passwords don't match");
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

    //check name fields validity
    check_name('firstName');
    check_name('lastName');

    //check if password valid
    check_password('regPassword');  
    //check if passwords matching
    is_password_match('regPassword','regPasswordConfirmation');
    
    if (!invalid){
        form.submit();
    };
    
  }

);






