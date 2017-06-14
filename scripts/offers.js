function getCurrentHighestOffer() {
    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == XMLHttpRequest.DONE) {
            if (xmlhttp.status == 200) {

                var block = document.createElement("DIV");
                block.style.fontSize = "20px";
                block.style.position = "relative";
                block.style.textAlign = "center";
                block.style.marginTop = "3%";
                block.style.marginBottom = "3%";
                block.style.order = 1;
                var price = document.createTextNode("Current maximum offer:  \u20AC " + JSON.parse(xmlhttp.responseText).amount + " ");
                block.appendChild(price);

                block.innerHTML += "offered by: " + JSON.parse(xmlhttp.responseText).user;
                document.getElementById("prices").appendChild(block);
            }
            else {
                console.log('Returned'+xmlhttp.status);
            }
        }
    };

    xmlhttp.open("GET", "/offers/", true);
    xmlhttp.send();
}


function getYourMaxOffer(user) {
    var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == XMLHttpRequest.DONE) {
            if (xmlhttp.status == 200) {
                var block = document.createElement("DIV");
                block.style.fontSize = "20px"
                block.style.position = "relative";
                block.style.textAlign = "center";
                block.style.marginTop = "3%";
                block.style.marginBottom = "3%";
                block.style.order = 2;
                var amount = JSON.parse(xmlhttp.responseText).amount;
                if (amount == 0) {
                    var noOffer = document.createTextNode("You didn't make any offer yet.");
                    block.appendChild(noOffer);
                } else {
                    var price = document.createTextNode("Your maximum offer:  \u20AC " + JSON.parse(xmlhttp.responseText).amount);
                    block.appendChild(price);
                }
                document.getElementById("prices").appendChild(block);


                //---------------------------------------------
                var highestBidder = JSON.parse(xmlhttp.responseText).highestBidder;
                var userIsMax = document.createElement("DIV");
                userIsMax.style.textAlign = "center";
                userIsMax.style.margin = "1%";
                var text = document.createElement("B");
                text.style.fontSize = "20px";

                if (user == highestBidder) {
                    text.style.color = "green";
                    text.innerText = 'You are the highest bidder!';
                } else 
                if ((user != highestBidder) && (amount != 0)) {
                    text.style.color = "red";
                    text.innerText = 'Your offer has been exceeded! You can make a new offer using the menu button.';
                } else {
                    if(user == "") {
                        text.style.color = "blue";
                        text.innerText = 'Welcome! Please, login or register.';
                    }
                }
                userIsMax.appendChild(text);
                document.getElementById("main").insertAdjacentElement("afterend", userIsMax);
                //-------------------------------------------------------


            }
            else {
                console.log('Returned '+xmlhttp.status);
            }
        }
    };

    xmlhttp.open("GET", "/offers/?THR=true", true);
    xmlhttp.send();

}