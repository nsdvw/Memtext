cp vendor/components/jquery/jquery.min.js web/vendor-js/jquery.min.js

cp vendor/twbs/bootstrap/dist/css/bootstrap.min.css web/vendor-css/bootstrap.min.css
cp vendor/twbs/bootstrap/dist/js/bootstrap.min.js web/vendor-js/bootstrap.min.js

for font in `ls vendor/twbs/bootstrap/dist/fonts/`
do
  cp "vendor/twbs/bootstrap/dist/fonts/$font" "web/fonts/$font"
done

cp -r vendor/ckeditor/ckeditor/adapters web/ckeditor/adapters
cp -r vendor/ckeditor/ckeditor/lang web/ckeditor/lang
cp -r vendor/ckeditor/ckeditor/plugins web/ckeditor/plugins
cp -r vendor/ckeditor/ckeditor/skins web/ckeditor/skins
cp vendor/ckeditor/ckeditor/ckeditor.js web/ckeditor/ckeditor.js
cp vendor/ckeditor/ckeditor/config.js web/ckeditor/config.js
cp vendor/ckeditor/ckeditor/contents.css web/ckeditor/contents.css
cp vendor/ckeditor/ckeditor/styles.js web/ckeditor/styles.js
