(function(){
    Date.prototype.deltaDays=function(c){
        return new Date(this.getFullYear(),this.getMonth(),this.getDate()+c)
    };
    Date.prototype.getSunday=function(){
        return this.deltaDays(-1*this.getDay())
    }
})();

function Week(c){
    this.sunday=c.getSunday();
    this.nextWeek=function(){
        return new Week(this.sunday.deltaDays(7))
    };
    this.prevWeek=function(){
        return new Week(this.sunday.deltaDays(-7))
    };
    this.contains=function(b){
        return this.sunday.valueOf()===b.getSunday().valueOf()
    };
    this.getDates=function(){
        for(var b=[],a=0;7>a;a++)b.push(this.sunday.deltaDays(a));
        return b
    }
}
function Month(c,b){
    this.year=c;
    this.month=b;
    this.nextMonth=function(){
        return new Month(c+Math.floor((b+1)/12),(b+1)%12)
    };
    this.prevMonth=function(){
        return new Month(c+Math.floor((b-1)/12),(b+11)%12)
    };
    this.getDateObject=function(a){
        return new Date(this.year,this.month,a)
    };
    this.getWeeks=function(){
        var a=this.getDateObject(1),b=this.nextMonth().getDateObject(0),c=[],a=new Week(a);
        for(c.push(a);!a.contains(b);)a=a.nextWeek(),c.push(a);
        return c
    }
};
//using isLogin() function to get user login information if logged in
isLogin().then(response => {
    if (response.success) {  
        
        addUserPanel();
        userid = response.userId;
        session_token = response.token;
        username_returned=response.userName;
    } else {
        userid = 0;
    }
    updateCalendar();//always update calendar
});
// For our purposes, we can keep the current month in a variable in the global scope
var currentMonth = new Month(2023, 2); // March 2023
var curDate=null;//set it to null when no date is chosen
function getCurDate(){
    return curDate;//get current date function
}
// Change the month when the next or previous month button is pressed
document.getElementById("next_month_btn").addEventListener("click", function(event){
    currentMonth = currentMonth.nextMonth(); // Previous month would be currentMonth.prevMonth()
    updateCalendar(); // Whenever the month is updated, we'll need to re-render the calendar in HTML
    //alert("The new month is "+currentMonth.month+" "+currentMonth.year);
}, false);
document.getElementById("pre_month_btn").addEventListener("click", function(event){
    currentMonth = currentMonth.prevMonth(); // Previous month would be currentMonth.prevMonth()
    updateCalendar(); // Whenever the month is updated, we'll need to re-render the calendar in HTML
    //alert("The new month is "+currentMonth.month+" "+currentMonth.year);
}, false);

//create an event for user himself and groupmembers of that event
function createEvent(){
    if(userid==0){
        alert("Log in first to add an event!");
    }
    //get all the event details from user input
    var eventCreation = document.getElementById("eventcreate-form");
    eTitle=eventCreation.querySelector('#eTitle').value;
    eContent=eventCreation.querySelector('#eContent').value;
    eStartTime=eventCreation.querySelector('#eStartTime').value;
    eEndTime=eventCreation.querySelector('#eEndTime').value;
    eDate=eventCreation.querySelector('#eDate').value;
    //fetch to transferEvent.php to create event for user himself
    uploadData = {'etitle':eTitle, 'econtent':eContent, 'etime_start': eStartTime, 'etime_end':eEndTime, 'edate':eDate, 'token':session_token};
    fetch('transferEvent.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json;charset=UTF-8'
        },
        body: JSON.stringify(uploadData)
    })
    .then(response => response.json())
    .catch(err => console.error(err));
    
    addGroupMember(eTitle, eContent,eStartTime, eEndTime, eDate, session_token);// add this events to all group members' calendars
    //empty all input area
    eventCreation.querySelector('#eTitle').value="";
    eventCreation.querySelector('#eContent').value="";
    eventCreation.querySelector('#eStartTime').value="";
    eventCreation.querySelector('#eEndTime').value="";
    eventCreation.querySelector('#eDate').value="";
    eventCreation.querySelector('#eGroup').value="";
    if(curDate!=null){//display events again if a date has been clicked
        display(curDate);
    }
}

//display all events associated with a date
function display(date){
    var display = document.getElementById("display");
    var dateData = new Date(currentMonth.year, (currentMonth.month), date);//stores the current date info
    //pass the date and CSRF token
    uploadData = {'date':dateData,'token':session_token};
    fetch('postEvent.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json;charset=UTF-8'
        },
        body: JSON.stringify(uploadData)
    })
    .then(response => response.json())
    .then(data =>{
       
        if(data.success == false){
            console.log("php Error");
        }
        else{
            //user logged in and the event list is successfully returned 
            var eventsBody = document.getElementById("eventsBody");
            eventsBody.innerHTML = ""; //empty what is shown first
            const events = data.events;//get an array of events in "events"
            events.forEach(event =>{
                //showing the title, content, start time, end time, and date of that event
                eventDiv = document.createElement("div");
                eventDiv.setAttribute('id', 'event'+event.eventid);
                eventDiv.setAttribute('style', 'border: 1px solid#7ab8cc;background-color:#fff;padding:15px');

                console.log(`Title: ${event.eventtitle}`);
                console.log(`Content: ${event.eventcontent}`);

                eventTitle = document.createElement("h2");
                eventTitle.setAttribute("id","title");
                eventTitle.textContent = event.eventtitle;

                eventContent = document.createElement("p");
                eventContent.setAttribute("id","content");
                eventContent.textContent = event.eventcontent;

                eventStart = document.createElement("p");
                eventStart.setAttribute("id","start");
                eventStart.textContent = event.eventstart;

                eventEnd = document.createElement("p");
                eventEnd.setAttribute("id","end");
                eventEnd.textContent = event.eventend;

                eventDate = document.createElement("p");
                eventDate.setAttribute("id","date");
                eventDate.textContent = event.eventdate;
                
                eventDiv.appendChild(eventTitle);
                eventDiv.appendChild(eventContent);
                eventDiv.appendChild(eventStart);
                eventDiv.appendChild(eventEnd);
                eventDiv.appendChild(eventDate);
                if(event.event_userid==userid){//if the event belongs to the user himself, he can edit or delete
                    // Create a new delete button element
                    delete_button = document.createElement('button');
                    // create delete and edit buttons
                    delete_button.setAttribute('type', 'button');
                    delete_button.setAttribute('id', 'delete_btn');
                    delete_button.value=event.eventid;
                    delete_button.innerHTML = 'DELETE EVENT';
                    eventDiv.appendChild(delete_button);
                    delete_button.onclick=function(){
                        //if click on delete button, calls deleteEvent function and delete that event according to event id
                        deleteEvent(event.eventid);
                    }
                    edit_button = document.createElement("button")
                    edit_button.setAttribute('type', 'button');
                    edit_button.setAttribute('id', 'edit_btn');
                    edit_button.value=event.eventid;
                    edit_button.innerHTML = 'EDIT EVENT';
                    eventDiv.appendChild(edit_button);

                    important_button = document.createElement("button")
                    important_button.setAttribute('type', 'button');
                    important_button.setAttribute('id', 'important_btn');
                    important_button.value=event.eventid;
                    if(event.eventimportant == 0){//if click on the star icon, change the importance mark of the event
                        //cicking to mark it importance or cancel the importance mark
                        important_button.innerHTML = '&#9734';
                    }
                    else{
                        important_button.innerHTML = '&#9733';
                    }
                    eventDiv.appendChild(important_button);
                    important_button.onclick=function(){//calls the function the change the importance mark stored in database
                        importantEvent(event.eventid);
                    }
                    //create form for editing the event details
                    newform = document.createElement('form');
                    newform.setAttribute('id', 'myedit-form');
                    event_editpart=document.createElement("div");
                    event_editpart.setAttribute('id','edit_div'+event.eventid);
                    eventDiv.appendChild(event_editpart);
                    event_editpart.appendChild(newform);
                    edit_button.onclick=function()
                    {
                        editEvent(event.eventid);//if click on edit button, calls editEvent function and edit that event details according to event id
                        
                    };
                    eventsBody.appendChild(eventDiv);
                }else{
                    //if the event is post by another user and the current user can only see but not edit or delete
                    //shows the username of whom post the events
                    eventOtherUser = document.createElement("p");
                    eventOtherUser.setAttribute("id","otherusername");
                    eventOtherUser.textContent = event.event_username;
                    eventDiv.appendChild(eventOtherUser);
                    eventsBody.appendChild(eventDiv);
                }
            });
        }
    }).catch(err => console.error(err));


}

//called when the star icon button is called and update the importance of that event in the database to the opposite value
function importantEvent(eventid){
    //pass event id and CSRF token
    uploadData = {'eid':eventid, 'token':session_token};
    console.log(JSON.stringify(uploadData));
    fetch('importantEvent.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json;charset=UTF-8'
        },
        body: JSON.stringify(uploadData)
    })
    .then(response => response.json())
    .then(data=>{
        //update the button's appearance
        important_button = document.getElementById('event'+eventid).querySelector("#important_btn");
        if(data.important == 0){
            important_button.innerHTML = '&#9734';
        }
        else
        {
            important_button.innerHTML = '&#9733';
        }
    }).catch(err => console.error(err));
    
}
function editEvent(eventid){
    //get all original details of event as shown

    oldtitle = document.getElementById("event"+eventid).querySelector("#title").innerHTML;
    oldcontent = document.getElementById("event"+eventid).querySelector("#content").innerHTML;
    oldstart = document.getElementById("event"+eventid).querySelector("#start").innerHTML;
    oldend = document.getElementById("event"+eventid).querySelector("#end").innerHTML;
    olddate = document.getElementById("event"+eventid).querySelector("#date").innerHTML;

    currentform = document.getElementById("edit_div"+eventid).querySelector("#myedit-form");
    currentform.innerHTML = "";//empty the form first

    //create the input area to edit the event and set original contents of them as details shown
    newtitle = document.createElement('input');
    newtitle.setAttribute('type', 'text');
    newtitle.setAttribute('id', 'newtitle');
    newtitle.setAttribute('required', '');
    newtitle.setAttribute('value', oldtitle);
    currentform.appendChild(newtitle);

    newcontent = document.createElement('input');
    newcontent.setAttribute('type', 'text');
    newcontent.setAttribute('id', 'newcontent');
    newcontent.setAttribute('required', '');
    newcontent.setAttribute('value', oldcontent);
    currentform.appendChild(newcontent);

    newstart = document.createElement('input');
    newstart.setAttribute('type', 'time');
    newstart.setAttribute('id', 'newstart');
    newstart.setAttribute('required', '');
    newstart.setAttribute('value', oldstart);
    currentform.appendChild(newstart);

    newend = document.createElement('input');
    newend.setAttribute('type', 'time');
    newend.setAttribute('id', 'newend');
    newend.setAttribute('required', '');
    newend.setAttribute('value', oldend);
    currentform.appendChild(newend);

    newdate = document.createElement('input');
    newdate.setAttribute('type', 'date');
    newdate.setAttribute('id', 'newdate');
    newdate.setAttribute('required', '');
    newdate.setAttribute('value', olddate);
    currentform.appendChild(newdate);

    submitbutton = document.createElement('button');
    submitbutton.setAttribute("type","submit");
    submitbutton.innerHTML="change";
    currentform.appendChild(submitbutton);
    //once the button is clicked, the function transferEditContent is called on that event according to event id
    document.getElementById("edit_div"+eventid).querySelector("#myedit-form").addEventListener("submit", event=>
    {
        event.preventDefault(); 
        transferEditContent(eventid);
    },false);
}

function transferEditContent(eventid){
    //get all the updated event details from user inputs
    eventEdit = document.getElementById("edit_div"+eventid).querySelector("#myedit-form");//这里id？？？
    eTitle=eventEdit.querySelector('#newtitle').value;
    eContent=eventEdit.querySelector('#newcontent').value;
    eStartTime=eventEdit.querySelector("#newstart").value;
    eEndTime=eventEdit.querySelector("#newend").value;
    eDate=eventEdit.querySelector("#newdate").value;
    
    //pass all details to php along with  event id and CSRF token
    uploadData = {'eid':eventid, 'etitle':eTitle, 'econtent':eContent, 'etime_start': eStartTime, 'etime_end':eEndTime, 'edate':eDate, 'token':session_token};
    fetch('editEvent.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json;charset=UTF-8'
        },
        body: JSON.stringify(uploadData)
    })
    .then(response => response.json())
    .then(data =>{
        if(curDate!=null){
            display(curDate);
        }
    }).then(data1 =>{
        
        updateCalendar();
    })
    .catch(err => console.error(err));
}

function deleteEvent(event_id){
    //pass the event id that is to be deleted and CSRF token
    uploadData = {'eid':event_id, 'token':session_token};
    fetch('deleteEvent.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json;charset=UTF-8'
        },
        body: JSON.stringify(uploadData)
    })
    .then(response => response.json())
    .then(data =>{
        if(data.success == false){
            console.log("php Error");
        }
        if(curDate!=null){
            display(curDate);//display events again if a date has been clicked
        }
        updateCalendar();
    }).catch(err => console.error(err));

    //
}


function updateCalendar() {

    var calendarBody = document.getElementById("calendar-body");
    var weeks = currentMonth.getWeeks();
    // Clear any existing calendar cells
    calendarBody.innerHTML = "";

    // Loop through each week and generate the cells for that week
    weeks.forEach(function(week) {
        var row = document.createElement("tr");
        var dates = week.getDates();

        // Loop through each date and generate a cell for that date
        dates.forEach(function(date) {
        var cell = document.createElement("td");

        cell.textContent = date.getDate();

        if(userid!=0){//if the user is logged in
            var dateData = new Date(date.getFullYear(), date.getMonth(), date.getDate());
            //select the number of events on that date that belongs to that user
            uploadData = {'date':dateData, 'userid':userid, 'token':session_token};
            fetch('countEvent.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json;charset=UTF-8'
                },
                body: JSON.stringify(uploadData)
            })
            .then(response => response.json())
            .then(data =>{
                //if there is any event on that date, display it as red
                if(data.count!=0 && date.getMonth() == currentMonth.month){
                    cell.setAttribute("style","color: #7ab8cc;");
                }
                if(data.important!=0 && date.getMonth() == currentMonth.month){
                    cell.setAttribute("style","color: red");
                }
            });
        }
         
        //create the button to change month
        if (date.getMonth() !== currentMonth.month) {
            cell.classList.add("other-month");
        }  else{
            cell.addEventListener("click", function(){curDate=date.getDate();display(date.getDate())}, false);
            cell.classList.add("this-month");
        }
        row.appendChild(cell);
        });

        calendarBody.appendChild(row);
    });

    // Update the month title
    var monthTitle = document.getElementById("month");
    monthTitle.textContent =  currentMonth.year+ "," + (currentMonth.month + 1);
}
