isLogin().then(response => {
    if (response.success) {
        userid = response.userId;
        username_session=response.userName;
        session_token = response.token;
    } else {
        userid = 0;
    }
});//returns the details of users if logged in

function addUserPanel(){
    //记得判断用户登录
    var display = document.getElementById("display");
    currentform = document.createElement('form');
    currentform.setAttribute('id', 'userlinked-form');

    linkPanel=document.createElement("div");
    linkPanel.setAttribute('id','userlink_div');
    //creates a division for linking user and append a form to that
    usernamelinked_input = document.createElement('input');//input box for typing linked usernames
    usernamelinked_input.setAttribute('type', 'text');
    usernamelinked_input.setAttribute('id', 'linkedusername');
    usernamelinked_input.setAttribute('required', '');
    currentform.appendChild(usernamelinked_input);
    
    submitbutton = document.createElement('button');//button to submit linked username
    submitbutton.setAttribute("type","submit");
    submitbutton.innerHTML="add other user to share their events";
    currentform.appendChild(submitbutton);

    linkPanel.appendChild(currentform);
    display.appendChild(linkPanel);

    document.getElementById("userlinked-form").addEventListener("submit", event=>
    {
        event.preventDefault(); //refreshing page
        addLinkedUser();
    },false);
}

function addLinkedUser(){
    
    linkUser= document.getElementById("userlink_div").querySelector("#userlinked-form");
    usernameLinked=document.getElementById("userlink_div").querySelector('#linkedusername').value;
    if(usernameLinked==userid){//if link to user himself, do nothing
        return;
    }
    uploadData = {'usernamelinked':usernameLinked, 'token':session_token};//pass usernamelinked and token
    //link user using fetch to php
    console.log(JSON.stringify(uploadData));
    fetch('linkUser.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json;charset=UTF-8'
        },
        body: JSON.stringify(uploadData)
    })
    .then(response => response.json())
    .then(data=>{
        if(data.success==false)
        {   
            if(data.message=="User Linked Does Not Exist!"){
                alert(data.message);
            }
        }
    }).catch(err => console.error(err));
    document.getElementById("userlink_div").querySelector('#linkedusername').value="";//empty the input box
    curDate_get=getCurDate();//refresh the page by loading display which displays events
    if(curDate_get!=null){
        display(curDate_get);
    }
}

//add that events for all users that are entered as sharing an event
function addGroupMember(eTitle, eContent,eStartTime, eEndTime, dateData, session_token){
    
    var input = document.getElementById('eGroup').value;
    if(input=="")return;
    var values = input.split(',');//separate usernames by comma
    memberarray=[];
    values.forEach(
        function (value)
        {
            if(value.trim()!=""&&value.trim()!=username_session){
                memberarray.push(value.trim());//push every nonempty involved member name without whitespace
            }
        }
    );
    if(memberarray==null)return ;
    //upload details of that event as well as a list containig all members of whom the event will be added to calendar
    uploadData = {'memberlist':memberarray,'etitle':eTitle, 'econtent':eContent, 'etime_start': eStartTime, 'etime_end':eEndTime, 'edate':dateData, 'token':session_token};
    fetch('addGroupEvent.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json;charset=UTF-8'
        },
        body: JSON.stringify(uploadData)
    })
    .then(response => response.json()).then(data => {
        if(data.message=="one user does not exist"){
            let alertmsg=data.message;
            emptylist=data.empty_list;
            emptylist.forEach(nonmember =>{
                alertmsg=alertmsg+nonmember;
            });
            alert(alertmsg);
        }
    }).catch(err => console.error(err));



}