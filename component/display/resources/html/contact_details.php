<div id="57e9d82c90630" class="candidate_inner_section formSection" type="open" inajax="">
    <div class="formFieldContainer fieldNamecontact_type[0] formFieldWidth1 ">
        <div class="formLabel">Type</div>
        <div class="formField">
            <select name="contact_type[0]" label="Type" inajax="" id="contact_type0Id">
                <option value="1">Home</option>
                <option value="2" selected="selected">Work</option>
                <option value="3">Web</option>
                <option value="4">Fax</option>
                <option value="5">Email</option>
                <option value="6">Mobile</option>
                <option value="7">Facebook</option>
                <option value="8">LinkedIn</option>
                <option value="9">Info</option>
                <option value="10">Skype address</option>
            </select>
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamecontact_value[0] formFieldWidth1 ">
        <div class="formLabel">Value</div>
        <div class="formField">
            <input type="text" name="contact_value[0]" style="padding-left:-500px" value="" inajax="" id="fldid_57e9d82c93e17">
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamecontact_visibility[0] formFieldWidth1 ">
        <div class="formField">
            <select name="contact_visibility[0]" class="hidden" visibility="hidden" type="hidden" onchange="if($(this).val() == 4){ $('.custom_vis0').fadeIn(); }else { $('.custom_vis0:visible').fadeOut(); } " inajax="" id="contact_visibility0Id">
                <option style="width:5px" value="1">Public</option>
                <option style="width:5px" value="2">Private</option>
                <option style="width:5px" value="3">My team</option>
                <option style="width:5px" value="4">Custom</option>
            </select>
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamesl_contactpk[0] formFieldWidth1  formFieldHidden ">
        <div class="formField">
            <input type="hidden" name="sl_contactpk[0]" value="0" inajax="" id="fldid_57e9d82c93f08">
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamegroupfk0 formFieldWidth1  custom_vis0 hidden ">
        <div class="formLabel">Quick select</div>
        <div class="formField">
            <select name="groupfk0" label="Quick select" onchange="

            $('#contact_userfk0Id').tokenInput('clear');
            $('#contact_userfk0Id').css('color', 'red');

            var asCons = $(this).val().split('||');
            //console.log(asCons);
            $(asCons).each(function(nIndex, sValue)
            {
              var asValue = sValue.split('@@');
              if(asValue.length == 2)
              {
                //console.log('adding user '+asValue[1]);
                $('#contact_userfk0Id').tokenInput('add', {id: asValue[0], name: asValue[1]});
              }
            });  " inajax="" id="groupfk0Id">
                <option value="101@@administrator">-</option>
                <option value="477@@||498@@||478@@||482@@respool||499@@||2@@watercooler"></option>
                <option value="442@@helpdesk||241@@fastars||240@@FATokyo||388@@lfry||448@@management||314@@mmoir||85@@msoni||99@@rkamo||312@@src||347@@ytakagi||199@@ykuwabara">Active user</option>
                <option value="388@@lfry||314@@mmoir||85@@msoni||99@@rkamo||347@@ytakagi||199@@ykuwabara">Active users</option>
                <option value="260@@dba||309@@nicholas||343@@rkiyamu||199@@ykuwabara">Administration</option>
                <option value="382@@akramer||481@@Cici||407@@it.group||443@@jkovaliovas||274@@lifescience||466@@ryago||490@@Sarah||315@@agent">Analyst</option>
                <option value="481@@Cici||462@@fhan||473@@||443@@jkovaliovas||431@@kkapur||388@@lfry||314@@mmoir||85@@msoni||493@@||276@@pthai||374@@pmiles||466@@ryago||354@@rpedersen||459@@ray||99@@rkamo||457@@saltanbagana||315@@agent||130@@vmaslyuk||347@@ytakagi">Consultant</option>
                <option value="442@@helpdesk||241@@fastars||240@@FATokyo||407@@it.group||274@@lifescience||448@@management||406@@mr||312@@src">Mailing list</option>
                <option value="314@@mmoir||85@@msoni||276@@pthai||459@@ray||354@@rpedersen||186@@ryan||99@@rkamo">Management</option>
                <option value="382@@akramer||481@@Cici||489@@||492@@||480@@pgreeff||466@@ryago||490@@Sarah||457@@saltanbagana||315@@agent||501@@srose">Researcher</option>
                <option value="489@@||276@@pthai||480@@pgreeff||459@@ray||354@@rpedersen||501@@srose">Slate Canada</option>
                <option value="186@@ryan">Slate Hong Kong</option>
                <option value="382@@akramer||186@@ryan||312@@src">Slate Manila</option>
                <option value="481@@Cici||462@@fhan||473@@||443@@jkovaliovas||431@@kkapur||388@@lfry||492@@||314@@mmoir||85@@msoni||406@@mr||494@@||309@@nicholas||276@@pthai||374@@pmiles||466@@ryago||459@@ray||354@@rpedersen||343@@rkiyamu||99@@rkamo||490@@Sarah||457@@saltanbagana||215@@tokyo||130@@vmaslyuk||347@@ytakagi||199@@ykuwabara">Slate Tokyo</option>
                <option value="101@@administrator||309@@nicholas">Support</option>
                <option value="442@@helpdesk||101@@administrator||300@@arinestine||260@@dba||241@@fastars||343@@rkiyamu||215@@tokyo||199@@ykuwabara">Team admin</option>
                <option value="374@@pmiles||130@@vmaslyuk">Team CNS</option>
                <option value="186@@ryan">Team finance</option>
                <option value="101@@administrator||473@@||274@@lifescience||314@@mmoir||466@@ryago||99@@rkamo||490@@Sarah||457@@saltanbagana||347@@ytakagi">Team healthcare</option>
                <option value="489@@||443@@jkovaliovas||492@@||459@@ray||354@@rpedersen">Team Industrial</option>
                <option value="481@@Cici||462@@fhan||407@@it.group||431@@kkapur||388@@lfry||85@@msoni||493@@||276@@pthai">Team IT</option>
                <option value="448@@management">Team the others</option>
            </select>
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamecontact_description[0] formFieldWidth1 ">
        <div class="formLabel">Notes</div>
        <div class="formField">
            <input type="text" name="contact_description[0]" value="" style="width:510px" inajax="" id="fldid_57e9d82c9400e">
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamecontact_userfk[0] formFieldWidth1  custom_vis0 hidden " id="user_block_0">
        <script language="javascript">
            $(document).ready(function() {
                $("#contact_userfk0Id").tokenInput("https://beta.slate.co.jp/index.php5?uid=579-704&ppa=ppasea&ppt=usr&ppk=0&show_id=0&friendly=1&active_only=1&pg=ajx", {
                    noResultsText: "no results found",
                    onResult: function(oResult) {
                        if (oResult.length == 0)
                            return [{
                                id: "token_clear",
                                name: "no result found"
                            }]

                        var oLast = $(oResult).last();
                        if (oLast && oLast[0] && oLast[0].callback)
                            eval(oLast[0].callback);

                        return oResult;
                    },
                    onAdd: function(oItem) {
                        // console.log(oItem);
                        if (oItem.id == "token_clear")
                            $(this).tokenInput("clear");


                    },
                    tokenFormatter: function(item) {
                        if (item.label)
                            return "<li class=test1 'contact_userfk[0]_item' title='" + item.title + "'><p>" + item.label + "</p></li>";
                        else
                            return "<li class=test2 'contact_userfk[0]_item' title='" + item.title + "'><p>" + item.name + "</p></li>";
                    },

                    tokenLimit: "10"
                });
            });
            $("#contact_userfk0Id").on("remove", function() {
                $("#contact_userfk0Id .token-input-dropdown-mac").remove();
            });
        </script>
        <div class="formLabel">Users</div>
        <div class="formField formAutocompleteContainer ">
            <ul class="token-input-list-mac">
                <li class="token-input-input-token-mac">
                    <input type="text" autocomplete="off" id="token-input-contact_userfk0Id" style="outline: none;">
                    <tester style="position: absolute; top: -9999px; left: -9999px; width: auto; font-size: 12px; font-family: Verdana, Arial, sans-serif; font-weight: 400; letter-spacing: 0px; white-space: nowrap;"></tester>
                </li>
            </ul>
            <input type="text" name="contact_userfk[0]" nbresult="10" url="https://beta.slate.co.jp/index.php5?uid=579-704&amp;ppa=ppasea&amp;ppt=usr&amp;ppk=0&amp;show_id=0&amp;friendly=1&amp;active_only=1&amp;pg=ajx" inajax="" id="contact_userfk0Id" value="" class="autocompleteField" onadd="" style="display: none;">
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldName57e9d82c930bc formFieldWidth1  formFieldHidden "><span inajax="" id="57e9d82c930bcId"></span>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldLineBreaker formFieldWidth1">&nbsp;</div>
    <div class="formFieldSeparator formFieldWidth1">&nbsp;</div>
    <div class="floatHack"></div>
    <div class="formFieldContainer fieldNamecontact_type[1] formFieldWidth1 ">
        <div class="formLabel">Type</div>
        <div class="formField">
            <select name="contact_type[1]" label="Type" inajax="" id="contact_type1Id">
                <option value="1">Home</option>
                <option value="2">Work</option>
                <option value="3">Web</option>
                <option value="4">Fax</option>
                <option value="5" selected="selected">Email</option>
                <option value="6">Mobile</option>
                <option value="7">Facebook</option>
                <option value="8">LinkedIn</option>
                <option value="9">Info</option>
                <option value="10">Skype address</option>
            </select>
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamecontact_value[1] formFieldWidth1 ">
        <div class="formLabel">Value</div>
        <div class="formField">
            <input type="text" name="contact_value[1]" style="padding-left:-500px" value="" inajax="" id="fldid_57e9d82c94177">
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamecontact_visibility[1] formFieldWidth1 ">
        <div class="formField">
            <select name="contact_visibility[1]" class="hidden" visibility="hidden" type="hidden" onchange="if($(this).val() == 4){ $('.custom_vis1').fadeIn(); }else { $('.custom_vis1:visible').fadeOut(); } " inajax="" id="contact_visibility1Id">
                <option style="width:5px" value="1">Public</option>
                <option style="width:5px" value="2">Private</option>
                <option style="width:5px" value="3">My team</option>
                <option style="width:5px" value="4">Custom</option>
            </select>
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamesl_contactpk[1] formFieldWidth1  formFieldHidden ">
        <div class="formField">
            <input type="hidden" name="sl_contactpk[1]" value="0" inajax="" id="fldid_57e9d82c9425d">
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamegroupfk1 formFieldWidth1  custom_vis1 hidden ">
        <div class="formLabel">Quick select</div>
        <div class="formField">
            <select name="groupfk1" label="Quick select" onchange="

            $('#contact_userfk1Id').tokenInput('clear');
            $('#contact_userfk1Id').css('color', 'red');

            var asCons = $(this).val().split('||');
            //console.log(asCons);
            $(asCons).each(function(nIndex, sValue)
            {
              var asValue = sValue.split('@@');
              if(asValue.length == 2)
              {
                //console.log('adding user '+asValue[1]);
                $('#contact_userfk1Id').tokenInput('add', {id: asValue[0], name: asValue[1]});
              }
            });  " inajax="" id="groupfk1Id">
                <option value="101@@administrator">-</option>
                <option value="477@@||498@@||478@@||482@@respool||499@@||2@@watercooler"></option>
                <option value="442@@helpdesk||241@@fastars||240@@FATokyo||388@@lfry||448@@management||314@@mmoir||85@@msoni||99@@rkamo||312@@src||347@@ytakagi||199@@ykuwabara">Active user</option>
                <option value="388@@lfry||314@@mmoir||85@@msoni||99@@rkamo||347@@ytakagi||199@@ykuwabara">Active users</option>
                <option value="260@@dba||309@@nicholas||343@@rkiyamu||199@@ykuwabara">Administration</option>
                <option value="382@@akramer||481@@Cici||407@@it.group||443@@jkovaliovas||274@@lifescience||466@@ryago||490@@Sarah||315@@agent">Analyst</option>
                <option value="481@@Cici||462@@fhan||473@@||443@@jkovaliovas||431@@kkapur||388@@lfry||314@@mmoir||85@@msoni||493@@||276@@pthai||374@@pmiles||466@@ryago||354@@rpedersen||459@@ray||99@@rkamo||457@@saltanbagana||315@@agent||130@@vmaslyuk||347@@ytakagi">Consultant</option>
                <option value="442@@helpdesk||241@@fastars||240@@FATokyo||407@@it.group||274@@lifescience||448@@management||406@@mr||312@@src">Mailing list</option>
                <option value="314@@mmoir||85@@msoni||276@@pthai||459@@ray||354@@rpedersen||186@@ryan||99@@rkamo">Management</option>
                <option value="382@@akramer||481@@Cici||489@@||492@@||480@@pgreeff||466@@ryago||490@@Sarah||457@@saltanbagana||315@@agent||501@@srose">Researcher</option>
                <option value="489@@||276@@pthai||480@@pgreeff||459@@ray||354@@rpedersen||501@@srose">Slate Canada</option>
                <option value="186@@ryan">Slate Hong Kong</option>
                <option value="382@@akramer||186@@ryan||312@@src">Slate Manila</option>
                <option value="481@@Cici||462@@fhan||473@@||443@@jkovaliovas||431@@kkapur||388@@lfry||492@@||314@@mmoir||85@@msoni||406@@mr||494@@||309@@nicholas||276@@pthai||374@@pmiles||466@@ryago||459@@ray||354@@rpedersen||343@@rkiyamu||99@@rkamo||490@@Sarah||457@@saltanbagana||215@@tokyo||130@@vmaslyuk||347@@ytakagi||199@@ykuwabara">Slate Tokyo</option>
                <option value="101@@administrator||309@@nicholas">Support</option>
                <option value="442@@helpdesk||101@@administrator||300@@arinestine||260@@dba||241@@fastars||343@@rkiyamu||215@@tokyo||199@@ykuwabara">Team admin</option>
                <option value="374@@pmiles||130@@vmaslyuk">Team CNS</option>
                <option value="186@@ryan">Team finance</option>
                <option value="101@@administrator||473@@||274@@lifescience||314@@mmoir||466@@ryago||99@@rkamo||490@@Sarah||457@@saltanbagana||347@@ytakagi">Team healthcare</option>
                <option value="489@@||443@@jkovaliovas||492@@||459@@ray||354@@rpedersen">Team Industrial</option>
                <option value="481@@Cici||462@@fhan||407@@it.group||431@@kkapur||388@@lfry||85@@msoni||493@@||276@@pthai">Team IT</option>
                <option value="448@@management">Team the others</option>
            </select>
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamecontact_description[1] formFieldWidth1 ">
        <div class="formLabel">Notes</div>
        <div class="formField">
            <input type="text" name="contact_description[1]" value="" style="width:510px" inajax="" id="fldid_57e9d82c9435c">
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamecontact_userfk[1] formFieldWidth1  custom_vis1 hidden " id="user_block_1">
        <script language="javascript">
            $(document).ready(function() {
                $("#contact_userfk1Id").tokenInput("https://beta.slate.co.jp/index.php5?uid=579-704&ppa=ppasea&ppt=usr&ppk=0&show_id=0&friendly=1&active_only=1&pg=ajx", {
                    noResultsText: "no results found",
                    onResult: function(oResult) {
                        if (oResult.length == 0)
                            return [{
                                id: "token_clear",
                                name: "no result found"
                            }]

                        var oLast = $(oResult).last();
                        if (oLast && oLast[0] && oLast[0].callback)
                            eval(oLast[0].callback);

                        return oResult;
                    },
                    onAdd: function(oItem) {
                        // console.log(oItem);
                        if (oItem.id == "token_clear")
                            $(this).tokenInput("clear");


                    },
                    tokenFormatter: function(item) {
                        if (item.label)
                            return "<li class=test1 'contact_userfk[1]_item' title='" + item.title + "'><p>" + item.label + "</p></li>";
                        else
                            return "<li class=test2 'contact_userfk[1]_item' title='" + item.title + "'><p>" + item.name + "</p></li>";
                    },

                    tokenLimit: "10"
                });
            });
            $("#contact_userfk1Id").on("remove", function() {
                $("#contact_userfk1Id .token-input-dropdown-mac").remove();
            });
        </script>
        <div class="formLabel">Users</div>
        <div class="formField formAutocompleteContainer ">
            <ul class="token-input-list-mac">
                <li class="token-input-input-token-mac">
                    <input type="text" autocomplete="off" id="token-input-contact_userfk1Id" style="outline: none;">
                    <tester style="position: absolute; top: -9999px; left: -9999px; width: auto; font-size: 12px; font-family: Verdana, Arial, sans-serif; font-weight: 400; letter-spacing: 0px; white-space: nowrap;"></tester>
                </li>
            </ul>
            <input type="text" name="contact_userfk[1]" nbresult="10" url="https://beta.slate.co.jp/index.php5?uid=579-704&amp;ppa=ppasea&amp;ppt=usr&amp;ppk=0&amp;show_id=0&amp;friendly=1&amp;active_only=1&amp;pg=ajx" inajax="" id="contact_userfk1Id" value="" class="autocompleteField" onadd="" style="display: none;">
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldName57e9d82c93481 formFieldWidth1  formFieldHidden "><span inajax="" id="57e9d82c93481Id"></span>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldLineBreaker formFieldWidth1">&nbsp;</div>
    <div class="formFieldSeparator formFieldWidth1">&nbsp;</div>
    <div class="floatHack"></div>
    <div class="formFieldContainer fieldNamecontact_type[2] formFieldWidth1 ">
        <div class="formLabel">Type</div>
        <div class="formField">
            <select name="contact_type[2]" label="Type" inajax="" id="contact_type2Id">
                <option value="1">Home</option>
                <option value="2">Work</option>
                <option value="3">Web</option>
                <option value="4">Fax</option>
                <option value="5">Email</option>
                <option value="6" selected="selected">Mobile</option>
                <option value="7">Facebook</option>
                <option value="8">LinkedIn</option>
                <option value="9">Info</option>
                <option value="10">Skype address</option>
            </select>
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamecontact_value[2] formFieldWidth1 ">
        <div class="formLabel">Value</div>
        <div class="formField">
            <input type="text" name="contact_value[2]" style="padding-left:-500px" value="" inajax="" id="fldid_57e9d82c9448e">
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamecontact_visibility[2] formFieldWidth1 ">
        <div class="formField">
            <select name="contact_visibility[2]" class="hidden" visibility="hidden" type="hidden" onchange="if($(this).val() == 4){ $('.custom_vis2').fadeIn(); }else { $('.custom_vis2:visible').fadeOut(); } " inajax="" id="contact_visibility2Id">
                <option style="width:5px" value="1">Public</option>
                <option style="width:5px" value="2">Private</option>
                <option style="width:5px" value="3">My team</option>
                <option style="width:5px" value="4">Custom</option>
            </select>
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamesl_contactpk[2] formFieldWidth1  formFieldHidden ">
        <div class="formField">
            <input type="hidden" name="sl_contactpk[2]" value="0" inajax="" id="fldid_57e9d82c9456f">
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamegroupfk2 formFieldWidth1  custom_vis2 hidden ">
        <div class="formLabel">Quick select</div>
        <div class="formField">
            <select name="groupfk2" label="Quick select" onchange="

            $('#contact_userfk2Id').tokenInput('clear');
            $('#contact_userfk2Id').css('color', 'red');

            var asCons = $(this).val().split('||');
            //console.log(asCons);
            $(asCons).each(function(nIndex, sValue)
            {
              var asValue = sValue.split('@@');
              if(asValue.length == 2)
              {
                //console.log('adding user '+asValue[1]);
                $('#contact_userfk2Id').tokenInput('add', {id: asValue[0], name: asValue[1]});
              }
            });  " inajax="" id="groupfk2Id">
                <option value="101@@administrator">-</option>
                <option value="477@@||498@@||478@@||482@@respool||499@@||2@@watercooler"></option>
                <option value="442@@helpdesk||241@@fastars||240@@FATokyo||388@@lfry||448@@management||314@@mmoir||85@@msoni||99@@rkamo||312@@src||347@@ytakagi||199@@ykuwabara">Active user</option>
                <option value="388@@lfry||314@@mmoir||85@@msoni||99@@rkamo||347@@ytakagi||199@@ykuwabara">Active users</option>
                <option value="260@@dba||309@@nicholas||343@@rkiyamu||199@@ykuwabara">Administration</option>
                <option value="382@@akramer||481@@Cici||407@@it.group||443@@jkovaliovas||274@@lifescience||466@@ryago||490@@Sarah||315@@agent">Analyst</option>
                <option value="481@@Cici||462@@fhan||473@@||443@@jkovaliovas||431@@kkapur||388@@lfry||314@@mmoir||85@@msoni||493@@||276@@pthai||374@@pmiles||466@@ryago||354@@rpedersen||459@@ray||99@@rkamo||457@@saltanbagana||315@@agent||130@@vmaslyuk||347@@ytakagi">Consultant</option>
                <option value="442@@helpdesk||241@@fastars||240@@FATokyo||407@@it.group||274@@lifescience||448@@management||406@@mr||312@@src">Mailing list</option>
                <option value="314@@mmoir||85@@msoni||276@@pthai||459@@ray||354@@rpedersen||186@@ryan||99@@rkamo">Management</option>
                <option value="382@@akramer||481@@Cici||489@@||492@@||480@@pgreeff||466@@ryago||490@@Sarah||457@@saltanbagana||315@@agent||501@@srose">Researcher</option>
                <option value="489@@||276@@pthai||480@@pgreeff||459@@ray||354@@rpedersen||501@@srose">Slate Canada</option>
                <option value="186@@ryan">Slate Hong Kong</option>
                <option value="382@@akramer||186@@ryan||312@@src">Slate Manila</option>
                <option value="481@@Cici||462@@fhan||473@@||443@@jkovaliovas||431@@kkapur||388@@lfry||492@@||314@@mmoir||85@@msoni||406@@mr||494@@||309@@nicholas||276@@pthai||374@@pmiles||466@@ryago||459@@ray||354@@rpedersen||343@@rkiyamu||99@@rkamo||490@@Sarah||457@@saltanbagana||215@@tokyo||130@@vmaslyuk||347@@ytakagi||199@@ykuwabara">Slate Tokyo</option>
                <option value="101@@administrator||309@@nicholas">Support</option>
                <option value="442@@helpdesk||101@@administrator||300@@arinestine||260@@dba||241@@fastars||343@@rkiyamu||215@@tokyo||199@@ykuwabara">Team admin</option>
                <option value="374@@pmiles||130@@vmaslyuk">Team CNS</option>
                <option value="186@@ryan">Team finance</option>
                <option value="101@@administrator||473@@||274@@lifescience||314@@mmoir||466@@ryago||99@@rkamo||490@@Sarah||457@@saltanbagana||347@@ytakagi">Team healthcare</option>
                <option value="489@@||443@@jkovaliovas||492@@||459@@ray||354@@rpedersen">Team Industrial</option>
                <option value="481@@Cici||462@@fhan||407@@it.group||431@@kkapur||388@@lfry||85@@msoni||493@@||276@@pthai">Team IT</option>
                <option value="448@@management">Team the others</option>
            </select>
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamecontact_description[2] formFieldWidth1 ">
        <div class="formLabel">Notes</div>
        <div class="formField">
            <input type="text" name="contact_description[2]" value="" style="width:510px" inajax="" id="fldid_57e9d82c9466c">
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamecontact_userfk[2] formFieldWidth1  custom_vis2 hidden " id="user_block_2">
        <script language="javascript">
            $(document).ready(function() {
                $("#contact_userfk2Id").tokenInput("https://beta.slate.co.jp/index.php5?uid=579-704&ppa=ppasea&ppt=usr&ppk=0&show_id=0&friendly=1&active_only=1&pg=ajx", {
                    noResultsText: "no results found",
                    onResult: function(oResult) {
                        if (oResult.length == 0)
                            return [{
                                id: "token_clear",
                                name: "no result found"
                            }]

                        var oLast = $(oResult).last();
                        if (oLast && oLast[0] && oLast[0].callback)
                            eval(oLast[0].callback);

                        return oResult;
                    },
                    onAdd: function(oItem) {
                        // console.log(oItem);
                        if (oItem.id == "token_clear")
                            $(this).tokenInput("clear");


                    },
                    tokenFormatter: function(item) {
                        if (item.label)
                            return "<li class=test1 'contact_userfk[2]_item' title='" + item.title + "'><p>" + item.label + "</p></li>";
                        else
                            return "<li class=test2 'contact_userfk[2]_item' title='" + item.title + "'><p>" + item.name + "</p></li>";
                    },

                    tokenLimit: "10"
                });
            });
            $("#contact_userfk2Id").on("remove", function() {
                $("#contact_userfk2Id .token-input-dropdown-mac").remove();
            });
        </script>
        <div class="formLabel">Users</div>
        <div class="formField formAutocompleteContainer ">
            <ul class="token-input-list-mac">
                <li class="token-input-input-token-mac">
                    <input type="text" autocomplete="off" id="token-input-contact_userfk2Id" style="outline: none;">
                    <tester style="position: absolute; top: -9999px; left: -9999px; width: auto; font-size: 12px; font-family: Verdana, Arial, sans-serif; font-weight: 400; letter-spacing: 0px; white-space: nowrap;"></tester>
                </li>
            </ul>
            <input type="text" name="contact_userfk[2]" nbresult="10" url="https://beta.slate.co.jp/index.php5?uid=579-704&amp;ppa=ppasea&amp;ppt=usr&amp;ppk=0&amp;show_id=0&amp;friendly=1&amp;active_only=1&amp;pg=ajx" inajax="" id="contact_userfk2Id" value="" class="autocompleteField" onadd="" style="display: none;">
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldName57e9d82c93843 formFieldWidth1  formFieldHidden "><span inajax="" id="57e9d82c93843Id"></span>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldLineBreaker formFieldWidth1">&nbsp;</div>
    <div class="formFieldSeparator formFieldWidth1">&nbsp;</div>
    <div class="floatHack"></div>
    <div class="formFieldContainer fieldNamecontact_type[3] formFieldWidth1 ">
        <div class="formLabel">Type</div>
        <div class="formField">
            <select name="contact_type[3]" label="Type" inajax="" id="contact_type3Id">
                <option value="1">Home</option>
                <option value="2">Work</option>
                <option value="3">Web</option>
                <option value="4">Fax</option>
                <option value="5">Email</option>
                <option value="6">Mobile</option>
                <option value="7">Facebook</option>
                <option value="8" selected="selected">LinkedIn</option>
                <option value="9">Info</option>
                <option value="10">Skype address</option>
            </select>
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamecontact_value[3] formFieldWidth1 ">
        <div class="formLabel">Value</div>
        <div class="formField">
            <input type="text" name="contact_value[3]" style="padding-left:-500px" value="" inajax="" id="fldid_57e9d82c94759">
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamecontact_visibility[3] formFieldWidth1 ">
        <div class="formField">
            <select name="contact_visibility[3]" class="hidden" visibility="hidden" type="hidden" onchange="if($(this).val() == 4){ $('.custom_vis3').fadeIn(); }else { $('.custom_vis3:visible').fadeOut(); } " inajax="" id="contact_visibility3Id">
                <option style="width:5px" value="1">Public</option>
                <option style="width:5px" value="2">Private</option>
                <option style="width:5px" value="3">My team</option>
                <option style="width:5px" value="4">Custom</option>
            </select>
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamesl_contactpk[3] formFieldWidth1  formFieldHidden ">
        <div class="formField">
            <input type="hidden" name="sl_contactpk[3]" value="0" inajax="" id="fldid_57e9d82c94850">
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamegroupfk3 formFieldWidth1  custom_vis3 hidden ">
        <div class="formLabel">Quick select</div>
        <div class="formField">
            <select name="groupfk3" label="Quick select" onchange="

            $('#contact_userfk3Id').tokenInput('clear');
            $('#contact_userfk3Id').css('color', 'red');

            var asCons = $(this).val().split('||');
            //console.log(asCons);
            $(asCons).each(function(nIndex, sValue)
            {
              var asValue = sValue.split('@@');
              if(asValue.length == 2)
              {
                //console.log('adding user '+asValue[1]);
                $('#contact_userfk3Id').tokenInput('add', {id: asValue[0], name: asValue[1]});
              }
            });  " inajax="" id="groupfk3Id">
                <option value="101@@administrator">-</option>
                <option value="477@@||498@@||478@@||482@@respool||499@@||2@@watercooler"></option>
                <option value="442@@helpdesk||241@@fastars||240@@FATokyo||388@@lfry||448@@management||314@@mmoir||85@@msoni||99@@rkamo||312@@src||347@@ytakagi||199@@ykuwabara">Active user</option>
                <option value="388@@lfry||314@@mmoir||85@@msoni||99@@rkamo||347@@ytakagi||199@@ykuwabara">Active users</option>
                <option value="260@@dba||309@@nicholas||343@@rkiyamu||199@@ykuwabara">Administration</option>
                <option value="382@@akramer||481@@Cici||407@@it.group||443@@jkovaliovas||274@@lifescience||466@@ryago||490@@Sarah||315@@agent">Analyst</option>
                <option value="481@@Cici||462@@fhan||473@@||443@@jkovaliovas||431@@kkapur||388@@lfry||314@@mmoir||85@@msoni||493@@||276@@pthai||374@@pmiles||466@@ryago||354@@rpedersen||459@@ray||99@@rkamo||457@@saltanbagana||315@@agent||130@@vmaslyuk||347@@ytakagi">Consultant</option>
                <option value="442@@helpdesk||241@@fastars||240@@FATokyo||407@@it.group||274@@lifescience||448@@management||406@@mr||312@@src">Mailing list</option>
                <option value="314@@mmoir||85@@msoni||276@@pthai||459@@ray||354@@rpedersen||186@@ryan||99@@rkamo">Management</option>
                <option value="382@@akramer||481@@Cici||489@@||492@@||480@@pgreeff||466@@ryago||490@@Sarah||457@@saltanbagana||315@@agent||501@@srose">Researcher</option>
                <option value="489@@||276@@pthai||480@@pgreeff||459@@ray||354@@rpedersen||501@@srose">Slate Canada</option>
                <option value="186@@ryan">Slate Hong Kong</option>
                <option value="382@@akramer||186@@ryan||312@@src">Slate Manila</option>
                <option value="481@@Cici||462@@fhan||473@@||443@@jkovaliovas||431@@kkapur||388@@lfry||492@@||314@@mmoir||85@@msoni||406@@mr||494@@||309@@nicholas||276@@pthai||374@@pmiles||466@@ryago||459@@ray||354@@rpedersen||343@@rkiyamu||99@@rkamo||490@@Sarah||457@@saltanbagana||215@@tokyo||130@@vmaslyuk||347@@ytakagi||199@@ykuwabara">Slate Tokyo</option>
                <option value="101@@administrator||309@@nicholas">Support</option>
                <option value="442@@helpdesk||101@@administrator||300@@arinestine||260@@dba||241@@fastars||343@@rkiyamu||215@@tokyo||199@@ykuwabara">Team admin</option>
                <option value="374@@pmiles||130@@vmaslyuk">Team CNS</option>
                <option value="186@@ryan">Team finance</option>
                <option value="101@@administrator||473@@||274@@lifescience||314@@mmoir||466@@ryago||99@@rkamo||490@@Sarah||457@@saltanbagana||347@@ytakagi">Team healthcare</option>
                <option value="489@@||443@@jkovaliovas||492@@||459@@ray||354@@rpedersen">Team Industrial</option>
                <option value="481@@Cici||462@@fhan||407@@it.group||431@@kkapur||388@@lfry||85@@msoni||493@@||276@@pthai">Team IT</option>
                <option value="448@@management">Team the others</option>
            </select>
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamecontact_description[3] formFieldWidth1 ">
        <div class="formLabel">Notes</div>
        <div class="formField">
            <input type="text" name="contact_description[3]" value="" style="width:510px" inajax="" id="fldid_57e9d82c94950">
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldNamecontact_userfk[3] formFieldWidth1  custom_vis3 hidden " id="user_block_3">
        <script language="javascript">
            $(document).ready(function() {
                $("#contact_userfk3Id").tokenInput("https://beta.slate.co.jp/index.php5?uid=579-704&ppa=ppasea&ppt=usr&ppk=0&show_id=0&friendly=1&active_only=1&pg=ajx", {
                    noResultsText: "no results found",
                    onResult: function(oResult) {
                        if (oResult.length == 0)
                            return [{
                                id: "token_clear",
                                name: "no result found"
                            }]

                        var oLast = $(oResult).last();
                        if (oLast && oLast[0] && oLast[0].callback)
                            eval(oLast[0].callback);

                        return oResult;
                    },
                    onAdd: function(oItem) {
                        // console.log(oItem);
                        if (oItem.id == "token_clear")
                            $(this).tokenInput("clear");


                    },
                    tokenFormatter: function(item) {
                        if (item.label)
                            return "<li class=test1 'contact_userfk[3]_item' title='" + item.title + "'><p>" + item.label + "</p></li>";
                        else
                            return "<li class=test2 'contact_userfk[3]_item' title='" + item.title + "'><p>" + item.name + "</p></li>";
                    },

                    tokenLimit: "10"
                });
            });
            $("#contact_userfk3Id").on("remove", function() {
                $("#contact_userfk3Id .token-input-dropdown-mac").remove();
            });
        </script>
        <div class="formLabel">Users</div>
        <div class="formField formAutocompleteContainer ">
            <ul class="token-input-list-mac">
                <li class="token-input-input-token-mac">
                    <input type="text" autocomplete="off" id="token-input-contact_userfk3Id" style="outline: none;">
                    <tester style="position: absolute; top: -9999px; left: -9999px; width: auto; font-size: 12px; font-family: Verdana, Arial, sans-serif; font-weight: 400; letter-spacing: 0px; white-space: nowrap;"></tester>
                </li>
            </ul>
            <input type="text" name="contact_userfk[3]" nbresult="10" url="https://beta.slate.co.jp/index.php5?uid=579-704&amp;ppa=ppasea&amp;ppt=usr&amp;ppk=0&amp;show_id=0&amp;friendly=1&amp;active_only=1&amp;pg=ajx" inajax="" id="contact_userfk3Id" value="" class="autocompleteField" onadd="" style="display: none;">
        </div>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldContainer fieldName57e9d82c93bfb formFieldWidth1  formFieldHidden "><span inajax="" id="57e9d82c93bfbId"></span>
        <div class="floatHack"></div>
    </div>
    <div class="formFieldLineBreaker formFieldWidth1">&nbsp;</div>
    <div class="formFieldSeparator formFieldWidth1">&nbsp;</div>
    <div class="floatHack"></div>
    <div class="floatHack"></div>
</div>