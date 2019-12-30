var app = {
    init: function() {      
        app.gridInit();
    },

    gridInit: function() {      
        
        for (let square = 1; square < 10; square++) {

            $('#grid').append("<div class=\"square\" id=" + square + ">");

            if (square < 4) {                 
                for (let row = 1; row < 4; row++) {                    
                    if (square == 1) {                        
                        for (let column = 1; column < 4; column++){
                            $('#' + square).append("<div class=\"cell\" id=" + square + "-" + row + "-" + column + ">");
                            $('#' + square + "-" + row + "-" + column).append("<input type=\"text\" name=\"" + square + "-" + row + "-" + column + "\" size=\"1\" maxlength=\"1\" class=\"input\"></div>");
                            $('#' + square + "-" + row + "-" + column).change(app.sendNumber());             
                        }                                                                   
                    }
                    if (square == 2) {
                        for (let column = 4; column < 7; column++){
                            $('#' + square).append("<div class=\"cell\" id=" + square + "-" + row + "-" + column + ">");
                            $('#' + square + "-" + row + "-" + column).append("<input type=\"text\" name=\"" + square + "-" + row + "-" + column + "\" size=\"1\" maxlength=\"1\" class=\"input\"></div>");
                            $('#' + square + "-" + row + "-" + column).change(app.sendNumber());                             
                        }                                                                      
                    }
                    if (square == 3) {
                        for (let column = 7; column < 10; column++){
                            $('#' + square).append("<div class=\"cell\" id=" + square + "-" + row + "-" + column + ">");
                            $('#' + square + "-" + row + "-" + column).append("<input type=\"text\" name=\"" + square + "-" + row + "-" + column + "\" size=\"1\" maxlength=\"1\" class=\"input\"></div>");
                            $('#' + square + "-" + row + "-" + column).change(app.sendNumber());   
                        }                                                                       
                    }
                }         
            }            
            if (square < 7) { 
                for (let row = 4; row < 7; row++) {
                    if (square == 4) {
                        for (let column = 1; column < 4; column++){
                            $('#' + square).append("<div class=\"cell\" id=" + square + "-" + row + "-" + column + ">");
                            $('#' + square + "-" + row + "-" + column).append("<input type=\"text\" name=\"" + square + "-" + row + "-" + column + "\" size=\"1\" maxlength=\"1\" class=\"input\"></div>");
                            $('#' + square + "-" + row + "-" + column).change(app.sendNumber());                             
                        }                                           
                    }
                    if (square == 5) {
                        for (let column = 4; column < 7; column++){
                            $('#' + square).append("<div class=\"cell\" id=" + square + "-" + row + "-" + column + ">");
                            $('#' + square + "-" + row + "-" + column).append("<input type=\"text\" name=\"" + square + "-" + row + "-" + column + "\" size=\"1\" maxlength=\"1\" class=\"input\"></div>");
                            $('#' + square + "-" + row + "-" + column).change(app.sendNumber());                            
                        }                                               
                    }
                    if (square == 6) {
                        for (let column = 7; column < 10; column++){
                            $('#' + square).append("<div class=\"cell\" id=" + square + "-" + row + "-" + column + ">");
                            $('#' + square + "-" + row + "-" + column).append("<input type=\"text\" name=\"" + square + "-" + row + "-" + column + "\" size=\"1\" maxlength=\"1\" class=\"input\"></div>");
                            $('#' + square + "-" + row + "-" + column).change(app.sendNumber());                             
                        }                                                
                    }
                }                                               
            }
            if (square < 10) { 
                for (let row = 7; row < 10; row++) {
                    if (square == 7) {
                        for (let column = 1; column < 4; column++){
                            $('#' + square).append("<div class=\"cell\" id=" + square + "-" + row + "-" + column + ">");
                            $('#' + square + "-" + row + "-" + column).append("<input type=\"text\" name=\"" + square + "-" + row + "-" + column + "\" size=\"1\" maxlength=\"1\" class=\"input\"></div>");
                            $('#' + square + "-" + row + "-" + column).change(app.sendNumber());                           
                        }                                           
                    }
                    if (square == 8) {
                        for (let column = 4; column < 7; column++){
                            $('#' + square).append("<div class=\"cell\" id=" + square + "-" + row + "-" + column + ">");
                            $('#' + square + "-" + row + "-" + column).append("<input type=\"text\" name=\"" + square + "-" + row + "-" + column + "\" size=\"1\" maxlength=\"1\" class=\"input\"></div>");
                            $('#' + square + "-" + row + "-" + column).change(app.sendNumber());                           
                        }                                               
                    }
                    if (square == 9) {
                        for (let column = 7; column < 10; column++){
                            $('#' + square).append("<div class=\"cell\" id=" + square + "-" + row + "-" + column + ">");
                            $('#' + square + "-" + row + "-" + column).append("<input type=\"text\" name=\"" + square + "-" + row + "-" + column + "\" size=\"1\" maxlength=\"1\" class=\"input\"></div>");
                            $('#' + square + "-" + row + "-" + column).change(app.sendNumber());                           
                        }                                                
                    }
                }
            }                       
        }
        //$('#' + square).append('<div>');                               
    },

    sendNumber : function() {
        var $request = $.ajax(
            {
                url: 'http://127.0.0.1:8001/',
                method: 'POST',
                dataType: 'json',                
            }
        );
        $request.done(app.saveResult); 
        $request.fail();        
        ; 
    },

    // getResult: function() {
    //     var $request = $.ajax(
    //         {
    //             url: 'http://christmashat.ddns.net/getresult/' + app.last_segment,
    //             method: 'GET',
    //             dataType: 'json',                
    //         }
    //     );
    //     $request.done(app.saveResult); 
    //     $request.fail();        
    //     ; 
    // },

    // saveResult(response) {
        
    //     var result = JSON.stringify(response.result);
    //     app.result = result.replace(/\"/g, "");
    //     app.interval = setInterval("app.divCountDown()",1000);
    // },

      };


$(document).ready(app.init);

  
  
  
  
  
  

