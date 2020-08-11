/*
 * © 2020 Henri Azevedo All Rights Reserved.
 */
"use strict";

const validate = function(form ,options){

    // Validates when trying to submit the form
    form.addEventListener('submit',function(e){
        e.preventDefault();

        var valid = true;

        if(form.querySelectorAll('.error') != undefined){
            form.querySelectorAll('.error').forEach(err => err.classList.remove('error'));
        }

        for(var opt in options){
            input = opt.toLowerCase().replace(/(?:^|\s)\S/g, function(a) { return a.toUpperCase(); });

            try{
                if(form.querySelector('[name="'+opt+'"]') != undefined){
                    checkInput(form,form.querySelector('[name="'+opt+'"]'),options[opt]);
                }
            }catch(err){
                valid = false;
                console.log(err.message);
                // Report validation errors
            }
        }

        if(valid){
            form.submit();
        }
    });

    // Validation when taking focus from the field
    if(form.querySelectorAll('input:not([type="submit"]),textarea')!=undefined){

        form.querySelectorAll('input:not([type="submit"]),textarea').forEach(
            input => input.addEventListener('blur',function(e){

                input.classList.remove('error');
                try{
                    checkInput(form,input,options[input.getAttribute('name')]);
                    // Removes error attributes from the field
                }catch(err){
                    console.log(err.message);
                    // Report validation errors
                }

            })
        );

    }

    function checkInput(f,input,rules){
        for(var rule in rules){

            fieldText = input.getAttribute('placeholder');

            var required = ((typeof rules['required']) === 'boolean') ? rules['required'] : false;

            switch(rule){

                case 'required':
                    if(required && input.value.length===0){
                        throw new Error(fieldText+' é obrigatório.');
                    }
                    break;

                case 'minlength':
                    if(required || input.value.length>0){
                        if(input.value.length<rules[rule]){
                            throw new Error(fieldText+' deve ter no mínimo '+rules[rule]+' caracteres.');
                        }
                    }
                    break;

                case 'maxlength':
                    if(required || input.value.length>0){
                        if(input.value.length>rules[rule]){
                            throw new Error(fieldText+' deve ter no mínimo '+rules[rule]['minlength']+' e no máximo '+value+' caracteres.');
                        }
                    }
                    break;

                case 'regex':
                    if(required || input.value.length>0){
                        if(!(new RegExp(rules[rule])).test(input.value)){
                            throw new Error(fieldText+' inválido(a).');
                        }
                    }
                    break;

                case 'equals':
                    if(required || input.value.length>0){
                        var clone = f.querySelector('[name="'+rules[rule]+'"]');

                        if(clone === undefined){
                            throw new Error(clone+' não encontrado para comparação.');
                        }

                        if(input.value!==clone.value){
                            throw new Error(fieldText+' está diferente de '+clone.getAttribute('placeholder')+'.');
                        }
                            
                    }
                    break;

            }
        }
    }
}


document.addEventListener('DOMContentLoaded',function(e){
    if(document.querySelectorAll('form')!=undefined){

        document.querySelectorAll('form[provider]').forEach(function(f,i){

            var data = new FormData();
            data.processData = false;
            data.append('provider',f.getAttribute('provider'));
            data.append('role',f.getAttribute('role'));

            if(self.fetch) {
                (async () => {
                    const rawResponse = await fetch('/validator', {
                        method: 'POST',
                        body: data
                      });
                      const response = await rawResponse.json();
                      if(typeof response == 'object'){
                          formWork(response);
                      }else{
                          console.log(response);
                      }
                })();
            } else {
                var xhr = new XMLHttpRequest();
                xhr.open( "POST", '/validator' , true );
                xhr.addEventListener('load',function(e){
                    if(isJson(xhr.response)){
                        response = JSON.parse(String(xhr.response));
                        formWork(response);
                    }
                });
                xhr.addEventListener('error',function(XMLHttpRequest,textStatus,errorThrown){
                    xhrError(XMLHttpRequest,textStatus,errorThrown);
                });
            }

            function formWork(response){

                for(var r in response) {
                    switch(r){

                        case 'success':
                            eval(response[r]);
                            break;

                        case 'error':
                            f.classList.add('disabled');
                            f.innerHTML = '<div class="panel-message error" style="display:block">'+response[r]+'</div>' + f.innerHTML;
                            break;
                    }
                }
            }

        });
    }
});

function isJson(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}


export default validate;
