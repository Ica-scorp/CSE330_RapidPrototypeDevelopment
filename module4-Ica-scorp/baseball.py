import re
import sys, os
from collections import OrderedDict
from decimal import Decimal
if len(sys.argv) < 2:
    sys.exit(f"Usage: {sys.argv[0]} filename")
email_regex = re.compile(r"([^\d\W]+ [^\d\W]+)(?: batted )(\d+)(?: times with )(\d+)(?: hits and )(?:\d+ runs)")
filename = sys.argv[1]
def find_all_email_domains(test):
	return email_regex.findall(test)
if not os.path.exists(filename):
    sys.exit(f"Error: File '{sys.argv[1]}' not found")
with open(filename) as f:
    file_contents = f.read()

#([^\d\W]+ [^\d\W]+)(?: batted )(\d+)(?: times with )(\d+)(?: hits and )(\d+)(?: runs)([^\d\W]+ [^\d\W]+)(?: batted )(\d+)(?: times with )(\d+)(?: hits and )(\d+)(?: runs)
data=find_all_email_domains(file_contents)
avg_score={

}
cnt={

}
theName=set()
for piece in data:
    person, time, hit =piece
    theName.add(person)
for p in theName:
    avg_score[p]=0
    cnt[p]=0
for piece in data:
    person, time, hit =piece
    avg_score[person]+=int(hit)
    cnt[person]+=int(time)
   
result={

}   
for ppl in theName:
    if cnt[ppl]==0:
        result[ppl]=round(0, 3)
    else:
        result[ppl]=round(avg_score[ppl]/cnt[ppl],3)
sorted_result=sorted(result.items(),reverse=True, key=lambda x:x[1])
for a in sorted_result:
    print("%s: %.3f" %(a[0], a[1]))