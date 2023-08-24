
# CSE330
Ica Chen/508302/Ica-scorp  
Dijkstra/508764/TJor-L  
Creative portion:  
1. The room creator can mute users in that room for 5 mins. After being muted, the user cannot send public messages, but only private messages. After the time limit is reached, the user is no longer muted and free to send messages. The mute period can add up: if we mute A at time 0, and mute A again at time 2, then for the entire period 0-7, A is muted.  
2. When a user logs in or logs out, everyone will have the message prompt on the top for some seconds.  
3. We allow user to upload png or jpeg images by clicking the selecting files button, the file will be automatically uploaded and shown in the room publicly without showing the user name. The image messages will not be muted by the creator. Uploading the images of the same names that have been uploaded will not be successful. Uploading image that has a very huge size may also fail. User can only see the image if they are in the room when the image is sent.  
4. The maximum number of messages shown is 7. Exceeding the limit, messages sent earlier will not be shown.  