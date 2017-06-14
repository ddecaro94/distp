function openNav() {
    var menu = document.getElementById('menu');
    menu.classList.add("open");
    menu.style.width = "250px";
    document.body.style.backgroundColor = "rgba(0,0,0,0.4)";
    setTimeout(function() {
        window.addEventListener("click", closeNav);
    }, 100);
}

function closeNav() {
    var menu = document.getElementById('menu');
    if (menu.classList.contains("open")){
        menu.classList.remove("open");
        menu.style.width = "0";
        menu.style.marginLeft= "0";
        document.body.style.backgroundColor = "white";
        setTimeout(function() {
           window.removeEventListener("click", closeNav); 
        }, 100);
        
    }
}
function switchNav(){
    if (document.getElementById("menu").classList.contains("open")){
        document.getElementById("menu").classList.remove("open");
        document.getElementById("menu").style.width = "0";
        document.getElementById("main").style.marginLeft= "0";
        document.body.style.backgroundColor = "white";
    } else {
        document.getElementById("menu").classList.add("open");
        document.getElementById("menu").style.width = "250px";
        document.getElementById("main").style.marginLeft = "250px";
        document.body.style.backgroundColor = "rgba(0,0,0,0.4)";
    }
}