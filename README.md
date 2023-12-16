# Airbnb API

## The idea is to get different csvs of Airbnb, create an import and build a website around it

What is currently working
- user registration
- user login
- user logout
- show paginated room inside city pages
- show single room page

What will be added in the next future:
- possibility to owners to claim their room and add more data
- phone verification on owner registration
- cache pagination count in Redis
- add amenities pages and show paginated relative rooms
- build more cvs imports from different sources
- gather more info for each room

### Tools
- [Postcode UK](https://postcodes.io)
- [Validation from migration](https://validationforlaravel.com/)
- https://nominatim.openstreetmap.org/reverse.php?lat=51.53398709799415&lon=-0.009004712536256327&zoom=12&format=xml
- https://api.3geonames.org/51.548295656447685,-0.48205784700094884.json