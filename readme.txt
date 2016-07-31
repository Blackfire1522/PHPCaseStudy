To have a working environment make sure, that the following lines are existing/not marked as comment in your php.ini

extension=php_openssl.dll
allow_url_fopen = On



The main file is "videos.php"


Additional remarks:
In order to avoid blowing the solution out of proportion, I decided to keep it down to the basics, so I didn't use any framework or anything similar. To guarantee at least a bit of modularity and reusability i outsourced the api-call functions in their own file.

If someone reads the provided code carefully, one might stumble upon a rather inelegant solution for the "load more" button. Since changing one CSS-Style for all 4 videos wouldn't work (only one additional video appeared instead of 4) i chose this solution, such that it would at least work properly. I would be glad, if somone could explain, why it wouldn't work with a single style and also what would be a better and more elegant solution.