cp vendor/components/jquery/jquery.min.js web/vendor-js/jquery.min.js

cp vendor/twbs/bootstrap/dist/css/bootstrap.min.css web/vendor-css/bootstrap.min.css
cp vendor/twbs/bootstrap/dist/js/bootstrap.min.js web/vendor-js/bootstrap.min.js

for font in `ls vendor/twbs/bootstrap/dist/fonts/`
do
  cp "vendor/twbs/bootstrap/dist/fonts/$font" "web/fonts/$font"
done
