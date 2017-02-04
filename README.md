Get car list 

curl -X GET -H "Content-Type: application/x-www-form-urlencoded" -H "Cache-Control: no-cache" "http://127.0.0.1/cars"

Get a car by id

curl -X GET -H "Content-Type: application/x-www-form-urlencoded" -H "Cache-Control: no-cache" "http://127.0.0.1/cars/1"

Update a record

curl -X PUT -H "Content-Type: application/x-www-form-urlencoded" -H "Authorization: Basic Og==" -H "Cache-Control: no-cache" -H -d 'Name=A-Star&Model=aaa&Year=aaaa&Electric=0' "http://127.0.0.1/cars/3?authToken=c3eb0065301c73c1c6aee205604babbc"

Delete a record

curl -X DELETE -H "Content-Type: application/x-www-form-urlencoded" -H "authToken: ec53f774f69a9ae38f845e4a61624cc8" -H "Authorization: Basic Og==" -H "Cache-Control: no-cache" -d 'Name=A-Star&Model=aaa&Year=aaaa&Electric=0' "http://127.0.0.1/cars/2?authToken=c3eb0065301c73c1c6aee205604babbc"


Create a new record

curl -X POST -H "Content-Type: application/x-www-form-urlencoded" -H "authToken: ec53f774f69a9ae38f845e4a61624cc8" -H "Authorization: Basic Og==" -H "Cache-Control: no-cache" -d 'Name=A-Star&Model=aaa&Year=aaaa&Electric=1' "http://127.0.0.1/cars/2?authToken=c3eb0065301c73c1c6aee205604babbc"

Code to get auth token to be passed as header

curl -X POST -H "Content-Type: application/x-www-form-urlencoded" -H "Authorization: Basic Og==" -H "Cache-Control: no-cache" -d 'Name=A-Star&Model=aaa&Year=aaaa&Electric=1' "http://127.0.0.1/cars?q=token"