pack:
	rm mageinn_module-dropship-1.0.2.zip & zip -r mageinn_module-dropship-1.0.2.zip ./ -x './.git/*' -x '.gitignore' -x './guide/*' -x './*.zip' -x './.idea/*' -x 'Makefile' -x 'readme.md' -x 'Dockerfile' -x 'cloudbuild.yaml'
