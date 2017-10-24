var row_selected = "";
var row_selecting = "ON";

$("button#add").click(add_astro);
$("button#mod").click(mod_astro);
$("button#del").click(del_astro);
$("#astro_tab tr:gt(0)").bind("click", select_row);
$("button#cancel").click(function(){
    $("div#form_modal").css("visibility", "collapse");
    $("#astro_form td.form_err").text("");
    $("p#doop_err").css("display", "none");});

function select_row(){
    if (row_selecting == "OFF"){ return;}
    var clk_row = $(this);
    var row_marked = $(clk_row).attr("data-id");
    $("#astro_tab tr.marked").removeClass("marked");
    if (row_selected == row_marked){
        row_selected = "";
        $("#mod, #del").attr("disabled", true);
        if ($("button#send").text() == "Změnit"){ $("button#send").attr("disabled", true);}
        return;
    }
    row_selected = row_marked;
    $(clk_row).addClass("marked");
    $("#mod, #del").attr("disabled", false);
    if ($("button#send").text() == "Změnit"){ mod_astro();}
}
       
function add_astro(){
    $("div#form_modal").css("visibility", "visible");
    $("button#send").text("Přidat").unbind().bind("click", send);
    $("button#send").attr("disabled", false);
}

function mod_astro(){
    $("div#form_modal").css("visibility", "visible");
    $("button#send").text("Změnit").unbind().bind("click", send);
    $("button#send").attr("disabled", false);
    $("#astro_form input[name = 'fname']").val($("tr.marked td.fname").text());
    $("#astro_form input[name = 'lname']").val($("tr.marked td.lname").text());
    var date = $("tr.marked td.DOB").text();
    date = date.split(".");
    var unzero = function(a){
        while (0==0){
            if (a.indexOf("0") != 0){ return a;}
            a = a.slice("1");
            return a;
        }
    }
    $("#astro_form input[name = 'DOB_day']").val(unzero(date[0]));
    $("#astro_form input[name = 'DOB_mon']").val(unzero(date[1]));
    $("#astro_form input[name = 'DOB_year']").val(unzero(date[2]));
    $("#astro_form textarea[name = 'skill']").text($("tr.marked td.skill").text())
}

function atro_form_extract(){
    this.fname = document.forms["astro_form"]["fname"].value;
    this.lname = document.forms["astro_form"]["lname"].value;
    this.DOB = [];
    this.DOB[0] = document.forms["astro_form"]["DOB_day"].value;
    this.DOB[1] = document.forms["astro_form"]["DOB_mon"].value;
    this.DOB[2] = document.forms["astro_form"]["DOB_year"].value;
    this.skill = document.forms["astro_form"]["skill"].value;
    this.task = $("button#send").text();
    this.row_id = row_selected;
}

function send(){
    var payload = new atro_form_extract;
    payload =  JSON.stringify(payload);
    $.get("/?do=send&payload=" + payload, function(resp){
        console.log(resp);
        if (resp.stat == "doop"){ $("p#doop_err").css("display", "inline");}
        $("td#fname_err").text(resp.fname);
        $("td#lname_err").text(resp.lname);
        $("td#DOB_err").text(resp.DOB);
        $("td#skill_err").text(resp.skill);
        if (resp.stat == "OK"){
            $("p#doop_err").css("display", "none");
            document.getElementById("reload").click();
    }
    });
}

function del_astro(){
    row_selecting = "OFF";
    $("div#modal_droprow_confirm").css("visibility", "visible");
    var name = $("tr.marked td:nth-of-type(1)").text() + " " + $("tr.marked td:nth-of-type(2)").text();
    $("span#dropping_astro_name").text(name);
    $("table.norm_tab tr.marked").css("background-color", "red");
    $("button#add, button#mod, button#del").attr("disabled", true);
    var drop_busted = function(){
        $("table.norm_tab tr.marked").removeAttr("style");
        $("table.norm_tab tr.marked").removeClass("selected");
        row_selected = "";
        $("button#add, button#mod, button#del").attr("disabled", false);
        $("div#modal_droprow_confirm").css("visibility", "collapse");
        row_selecting = "ON";
    };
    var drop_confirmed = function(){
        $.get("/?do=del&row=" + row_selected, function(){
            document.getElementById("reload").click();
        });
    };
    $("button#row_drop_yes").unbind().bind("click", drop_confirmed);
    $("button#row_drop_no").unbind().bind("click", drop_busted);
}

