/*Popup Menu*/
var popupcode = `
var popupBody = document.createElement('div');
popupBody.setAttribute('id', 'popup');
popupBody.innerHTML = html;

var body = document.body;
if (body.firstChild) {
    body.insertBefore(popupBody, body.firstChild);
} else {
    body.appendChild(popupBody);
}
`;



function popup(head, body,back) {
    var html = `
    <div class="calbg"></div>
    <div class="calert col ">
    <div class="btna" style="width:100%;">
    <h3 class="fll">${head}</h3>
    <svg width="18" height="18" class="flr" color="red" id="closePopup" style="position:absolute;top:3px;right:4px;">
         <use xlink:href="../img/all.svg#close"></use>
    </svg>
    </div>
    <div>
    ${body}
    </div>
    </div>
    `;
    eval(popupcode);

    document.getElementById('closePopup').addEventListener('click', function () {
        
        if (back == 'reload') {
            location.reload();
        } else if(back == 'noreload'){
            document.getElementById('popup').innerHTML = "";
        }else if(typeof back !== 'undefined'){
            window.onbeforeunload = null;
            window.location.href = back;
        }else if(typeof back == undefined){
            window.location.href = window.location.pathname;
        }
    });

}

function popupIframe(head,body,back){
        var html = `
        <div class="calbg"></div>
        <div class="calert falert col">
        <div class="btna" style="width:100%;">
        <h3 class="fll">${head}</h3>
        <svg width="18" height="18" class="flr" color="red" id="closePopup" style="position:absolute;top:3px;right:4px;">
             <use xlink:href="../img/all.svg#close"></use>
        </svg>
        </div>
        <div>
        ${body}
        </div>
        </div>
        `;

        eval(popupcode);

        document.getElementById('closePopup').addEventListener('click', function () {
            if (typeof back !== 'undefined') {
                window.onbeforeunload = null;
                window.location.href = back;
            } else {
                document.getElementById('popup').innerHTML = "";
            }
           
        });
}
