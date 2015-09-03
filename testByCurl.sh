
echo "Enter Cookie:"
read cookie

echo "Testing API/V1"
curl -b $cookie http://hawkerhub.quanyang.me/api/v1
echo "\nTesting POST api/v1/users/deauthorize"
curl -d "" -b $cookie http://hawkerhub.quanyang.me/api/v1/users/deauthorize
echo "\nTesting POST api/v1/users/settings"
curl -d "" -b $cookie http://hawkerhub.quanyang.me/api/v1/users/settings
echo "\nTesting GET api/v1/users/login"
curl -b $cookie http://hawkerhub.quanyang.me/api/v1/users/login
echo "\nTesting POST api/v1/users/login"
curl -d "" -b $cookie http://hawkerhub.quanyang.me/api/v1/users/login
echo "\nTesting GET api/v1/users/1/item/recent"
curl -b $cookie http://hawkerhub.quanyang.me/api/v1/item/recent
echo "\nTesting GET api/v1/item?limit=1"
curl -b $cookie "http://hawkerhub.quanyang.me/api/v1/item?limit=1"
echo "\nTesting POST api/v1/item"
curl -d "" -b $cookie http://hawkerhub.quanyang.me/api/v1/item
echo "\nTesting GET api/v1/item/search?keyword=test&limit=1"
curl -b $cookie "http://hawkerhub.quanyang.me/api/v1/item/search?keyword=test&limit=1"
echo "\nTesting GET api/v1/item/1"
curl -b $cookie http://hawkerhub.quanyang.me/api/v1/item/1
echo "\nTesting DELETE api/v1/item/2"
curl -X "DELETE" -b $cookie http://hawkerhub.quanyang.me/api/v1/item/2
echo "\nTesting GET api/v1/item/1/like"
curl -b $cookie http://hawkerhub.quanyang.me/api/v1/item/1/like
echo "\nTesting POST api/v1/item/1/like"
curl -d "" -b $cookie http://hawkerhub.quanyang.me/api/v1/item/1/like
echo "\nTesting DELETE api/v1/item/1/like"
curl -X "DELETE" -b $cookie http://hawkerhub.quanyang.me/api/v1/item/1/like
echo "\nTesting GET api/v1/item/1/comment"
curl -b $cookie http://hawkerhub.quanyang.me/api/v1/item/1/comment
echo "\nTesting POST api/v1/item/1/comment"
curl -d "" -b $cookie http://hawkerhub.quanyang.me/api/v1/item/1/comment
echo "\nTesting DELETE api/v1/item/1/comment"
curl -X "DELETE" -b $cookie http://hawkerhub.quanyang.me/api/v1/item/1/comment
echo "\nTesting POST api/v1/item/photo"
curl -d "" -b $cookie http://hawkerhub.quanyang.me/api/v1/item/photo
echo "\nTesting GET api/v1/item/photo/1"
curl -b $cookie http://hawkerhub.quanyang.me/api/v1/item/photo/1
