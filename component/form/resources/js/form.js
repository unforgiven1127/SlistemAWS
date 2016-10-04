/**
 * Comment
 */



function ShowDialogBox(title, content, btn1text, btn2text, functionText, parameterList)
{
    var btn1css;
    var btn2css;

    if (btn1text == '') {
        btn1css = "hidecss";
    } else {
        btn1css = "showcss";
    }

    if (btn2text == '') {
        btn2css = "hidecss";
    } else {
        btn2css = "showcss";
    }
    content = "There are possible duplicates:<br><br>"+content+"<br><br>Do you still want to add a new company?";
    $("#lblMessage").html(content);

    $("#dialog").dialog({
        resizable: true,
        title: title,
        modal: true,
        width: '500px',
        height: '400px',
        overflow: 'auto',
        bgiframe: false,
        hide: { effect: 'scale', duration: 400 },

        buttons: [
                    {
                      text: btn1text,
                      "class": btn1css,
                      click: function () {
                          var oldUrl = $('#addcompanyId').attr('action');
                          var newUrl = oldUrl+'&mailFlg=yes';
                          $('#addcompanyId').attr('action',newUrl);
                          $('#addcompanyId').submit();
                          $("#dialog").dialog('close');

                      }
                    },
                    {
                      text: btn2text,
                      "class": btn2css,
                      click: function () {
                          var oldUrl = $('#addcompanyId').attr('action');
                          var newUrl = oldUrl+'&mailFlg=no';
                          $('#addcompanyId').attr('action',newUrl);
                          $('#addcompanyId').submit();
                          $("#dialog").dialog('close');
                      }
                    }
                ]
    });
}

function loading()
{
  alert('loading');
    $('body').addClass('noScroll').append('<div id="slLoadingScreen"  style="z-index: 99999; width: '+ ($(document).innerWidth() + 100) +'px; height: '+ ($(document).innerHeight() + 100) +'px; position: absolute; top: 0; left: 0; "><div class="bg"></div><div class="ani"></div></div>');
    $('body').append("<div id='overlay' class='overlay'></div>");
}

function beforeCompanyAdd()
{

    var companyName = $('.companyNameClass').val();
    //$('.ui-dialog').attr('id', 'companyAddNewId')
    //$('.ui-dialog').addClass("loadClass");

    //alert(companyName);
    //loading();
    psUrl = 'index.php5?uid=555-001&ppa=cdc&ppt=candi&ppk=0&pg=ajx';

    console.log(psUrl);
    $.ajax({
      type: 'POST',
      url: psUrl,
      scriptCharset: "utf-8" ,
      data: {cname:companyName},
      contentType: "application/x-www-form-urlencoded; charset=UTF-8",
      success: function(oJsonData)
      {
          //$('.ui-dialog').removeClass("loadClass");
          //alert('Success');
          //$('#slLoadingScreen').remove();

          //console.log(oJsonData);
          var data = oJsonData.data;
          var parsedData = jQuery.parseJSON(data);
          if(parsedData != "none")
          {
            ShowDialogBox('Warning',parsedData,'Yes','No', 'GoToAssetList',null);
            /*var newUrl = form.action+'&mailFlg=no';
            $('#addcompanyId').attr('action',newUrl);*/// mail gondermesi icin alan ekledik
            /*var msg = "There are possible duplicates: "+parsedData+" do you still want to add a new company?";
            if(ShowDialogBox(msg))
            {
                //alert('yes');
                //event.preventDefault();
                var newUrl = form.action+'&mailFlg=yes';
                $('#addcompanyId').attr('action',newUrl);// mail gondermesi icin alan ekledik
                //$('#addcompanyId').submit();
                return true;
            }
            else
            {
                //alert('no');
                var newUrl = form.action+'&mailFlg=no';
                $('#addcompanyId').attr('action',newUrl);// mail gondermesi icin alan ekledik
                return false;
            }*/
          }
          else
          {
            $('#addcompanyId').submit();
          }

          //alert(parsedData);
          //var data = oJsonData.data;
          //alert(data);
          //console.log(data);
        //$(psToPrepend).append(oJsonData.data);
      },
      async: false,
      dataType: "JSON"
    });
  //return false;

  //alert('END');
  //console.log(form);
}

function toggleSection(poTitle, psSectionId)
{
  if($(poTitle).hasClass('sectionClosed'))
  {
    $('#'+psSectionId).fadeIn(250, function()
    {
      $(poTitle).addClass('sectionOpened');
      $(poTitle).removeClass('sectionClosed');
    });

    $('#'+psSectionId+' .form_field_inactive').removeClass('field_inactive');
    $('#'+psSectionId+' .form_field_inactive').removeAttr('disabled');
  }
  else
  {
    $('#'+psSectionId).fadeOut(250, function()
    {
      $(poTitle).addClass('sectionClosed');
      $(poTitle).removeClass('sectionOpened');
    });

    $('#'+psSectionId+' .form_field_inactive').addClass('field_inactive');
    $('#'+psSectionId+' .form_field_inactive').attr('disabled', 'disabled');
  }

  return true;
}

$(document).ready(function() {

  $('input[onfinishinput]').keypress(function(e)
	{
		startTimer($(e.target));
	});


});

var inputTimeout;
function startTimer(input_field)
{
  var timeout = input_field.attr("inputtimeout");

  if (timeout.lenght==0)
    timeout = 1000;

  if (inputTimeout != undefined)
		clearTimeout(inputTimeout);

	inputTimeout = setTimeout( function()
  {
    eval(input_field.attr("onfinishinput"));
  }
	, timeout);
}

/**
 * Checks if the input is an email adress
 */
function isEmail(input)
{
  input= $.trim(input);

  var regExp = new RegExp("[a-zA-Z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])","");

  return regExp.test(input);
}


/**
 * Checks if the input is a phone number
 */
function isPhoneNumber(input)
{
  input= $.trim(input);
  var regExp = new RegExp("^[0-9]{3}-[0-9]{3}-[0-9]{4}|[0-9]{3}-[0-9]{4}-[0-9]{4}|[0-9]{10}|[0-9]{11}|[0-9]{2}-[0-9]{4}-[0-9]{4}$");

  console.log('phone number:'+regExp.test(input));
  return regExp.test(input);
}

/**
 * Checks if it is a date
 */
function isDate(input)
{
  input= $.trim(input);

  var regExp1 = new RegExp("^([0-2][0-9]|3[0-1])-(0[0-9]|1[0-2])-[0-9]{4}$"); // 24-01-2001
  var regExp2 = new RegExp("^([0-2][0-9]|3[0-1])/(0[0-9]|1[0-2])/[0-9]{4}$"); // 24/01/2001
  var regExp3 = new RegExp("^[0-9]{4}-(0[0-9]|1[0-2])-([0-2][0-9]|3[0-1])$"); // 2001/01/24
  var regExp4 = new RegExp("^[0-9]{4}/(0[0-9]|1[0-2])/([0-2][0-9]|3[0-1])$"); // 2001-01-24

  var output = (regExp4.test(input) | regExp3.test(input) | regExp2.test(input) | regExp1.test(input));

  console.log('date:'+output);
  return output;
}

/**
 * Checks if it is an address Japanese format
 */
function isAddress(input)
{
  input= $.trim(input);

  var regExp = new RegExp(".+to.+ku.+[0-9]{1,2}-[0-9]{1,2}");
  console.log('address:'+regExp.test(input));
  return regExp.test(input);
}