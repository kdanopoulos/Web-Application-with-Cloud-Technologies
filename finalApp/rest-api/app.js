const express = require('express');
const mongoose = require('mongoose');
const bodyParser = require('body-parser');
const cors = require('cors');

const app = express();

//Database Schemas
const Cinemas = require('./models/Cinemas');
const Movies = require('./models/Movies');
const Favorites = require('./models/Favorites');
const Notifications = require('./models/Notifications');

//Middleware
app.use(cors());
app.use(bodyParser.json());


//ROUTES
app.get('/test',(req,res) =>{
  console.log('someone send my /test');
  res.send('We are on home');
});
app.post('/test',(req,res) =>{
  console.log('someone send my /test');
  console.log("body = ");
  console.log(req.body);
  res.send('We are on home');
});

//------------------------- add Notification -------------------------
app.post('/notifications/add',async (req,res) =>{
  try{
    const myNotification = await Notifications.findOne({ movie_id: req.body.movie_id });
    if(myNotification){
      // Notification exists
      console.log('Notification exists');
      const updateNotification = await Notifications.updateOne({ movie_id: req.body.movie_id },
        { $set: {
            title: req.body.title,
            start_date: req.body.start_date,
            end_date: req.body.end_date,
            category: req.body.category,
            users_id: []
        } });
      const fav = await Favorites.find({ movie_id: req.body.movie_id });
      for(let curFav of fav){
          var new_user_id = { id: curFav.user_id };
          const pushUser = await Notifications.updateOne({ movie_id: req.body.movie_id },
            { $push: { users_id: new_user_id }} );
      }
      res.json(updateNotification);
    }else{
      // Notification doesn't exists
      console.log('Notification dont exists');
      const myMovie = await Movies.findOne({ _id: req.body.movie_id });
      const myCinema = await Cinemas.findOne({ _id: myMovie.cinema_id });
      const newNotification = new Notifications({
        title: req.body.title,
        start_date: req.body.start_date,
        end_date: req.body.end_date,
        category: req.body.category,
        cinema_name: myCinema.name,
        movie_id: req.body.movie_id
      });
      const savedNotification = await newNotification.save();
      const fav = await Favorites.find({ movie_id: req.body.movie_id });
      for(let curFav of fav){
        var new_user_id = { id: curFav.user_id };
        const pushUser = await Notifications.updateOne({ movie_id: req.body.movie_id },
          { $push: { users_id: new_user_id }} );
      }
      res.json(pushUser);
    }
  }catch(err){
    console.log('inside catch');
    res.json({ message: err});
  }
});
//------------------------- add Notification -------------------------\\ end
//------------------------- print all Notifications -------------------------
app.get('/notifications/all',async (req,res) =>{ // print all
  try{
    const notifications = await Notifications.find();  // find all Notifications from db
    res.json(notifications);
  }catch(err){
    res.json({ message: err});
  }
});
//------------------------- print all Notifications -------------------------\\ end
//------------------------- delete Notification -------------------------
app.delete('/notifications/delete',async (req,res) =>{
  try {
    const removeNotif = await Notifications.remove({ _id: req.body.id });
    res.json(removeNotif);
  } catch (err) {
    res.json({ message: err});
  }
});
//------------------------- delete Notification -------------------------\\ end
//------------------------- delete an id from Notification -------------------------
app.delete('/notifications/delete/id',async (req,res) =>{
  console.log(req.body);
  try {
    var old_user_id = { id: req.body.user_id };
    const pullUser = await Notifications.updateOne({ movie_id: req.body.movie_id },
      { $pull: { users_id: old_user_id }} );
    res.json();
  } catch (err) {
    res.json({ message: err});
  }
});
//------------------------- delete an id from Notification -------------------------\\ end
//------------------------- delete favorite -------------------------
app.delete('/favorites/delete',async (req,res) =>{
  try {
    const removeFav = await Favorites.remove({ user_id: req.body.user_id , movie_id: req.body.movie_id });
    res.json(removeFav);
  } catch (err) {
    res.json({ message: err});
  }
});
//------------------------- delete favorite -------------------------\\ end
//------------------------- add cinema -------------------------
app.post('/cinemas',async (req,res) =>{ // add cinema
  //console.log(req.body);
  const newCinema = new Cinemas({
    owner_id: req.body.owner_id,
    owner_name: req.body.owner_name,
    name: req.body.name
  });
  try {
    const savedCinema = await newCinema.save();
    res.json(savedCinema);
  } catch (err) {
    res.json({ message: err});
  }
});
//------------------------- add cinema -------------------------\\ end
//------------------------- edit cinema with cinema_name -------------------------
app.put('/cinema/edit',async (req,res) =>{
  try {
    const updateCinema = await Cinemas.updateOne({ name: req.body.cinema_name },
      { $set: {
          owner_id: req.body.new_onwer_id,
          owner_name: req.body.new_onwer_name
      } });
    res.json(updateMovie);
  } catch (err) {
    res.json({ message: err});
  }
});
//------------------------- edit cinema with cinema_name -------------------------\\ end
//------------------------- add movie --------------------------
app.post('/movies',async (req,res) =>{ // add movie
  console.log(req.body);
  const newMovie = new Movies({
    title: req.body.title,
    start_date: new Date(req.body.start_date),
    end_date: new Date(req.body.end_date),
    cinema_id: req.body.cinema_id,
    category: req.body.category
  });
  try {
    const savedMovie = await newMovie.save();
    res.json(savedMovie);
  } catch (err) {
    res.json({ message: err});
  }
});
//------------------------- add movie --------------------------\\ end
//------------------------- add movie by cinema_owner_id --------------------------
app.post('/movies/cinema_owner_id',async (req,res) =>{ // add movie
  console.log(req.body);
  try {
    const cinema = await Cinemas.findOne({ owner_id: req.body.owner_id });
    const newMovie = new Movies({
      title: req.body.title,
      start_date: new Date(req.body.start_date),
      end_date: new Date(req.body.end_date),
      cinema_id: cinema._id,
      category: req.body.category
    });
    const savedMovie = await newMovie.save();
    res.json(savedMovie);
  } catch (err) {
    res.json({ message: err});
  }
});
//------------------------- add movie by cinema_owner_id --------------------------\\ end
//------------------------- add favorite -------------------------
app.post('/favorites',async (req,res) =>{ // add favorite
  console.log(req.body);
  const newfavorite = new Favorites({
    user_id: req.body.user_id,
    movie_id: req.body.movie_id
  });
  try {
    const savedFavorite = await newfavorite.save();
    res.json(savedFavorite);
  } catch (err) {
    res.json({ message: err});
  }
});
//------------------------- add favorite -------------------------\\ end
//------------------------- print all Movies -------------------------
app.get('/movies/all',async (req,res) =>{ // print all
  var user_id = req.headers['user_id'];
  console.log(user_id);
  var anwser = [];
  try{
    const movies = await Movies.find();  // find all movies from db
    for(let curMovie of movies){
      const cinema = await Cinemas.findById(curMovie.cinema_id); // find cinema for current movie
      const fav = await Favorites.findOne({ movie_id: curMovie._id , user_id: user_id  });
      var value_fav = -1;
      if(fav==null){
        value_fav = 0;
      }
      else{
        value_fav = 1;
      }
      var singleMovie = {
        movie_id: curMovie._id,
        title: curMovie.title,
        start_date: curMovie.start_date,
        end_date: curMovie.end_date,
        category: curMovie.category,
        cinema: cinema.name,
        owner: cinema.owner_name,
        favorite: value_fav
      };
      anwser.push(singleMovie);
    }
    res.json(anwser);
  }catch(err){
    res.json({ message: err});
  }
});
//------------------------- print all Movies -------------------------\\ end
//------------------------- get my favorite Movies id -------------------------
app.get('/movies/all/favorite/id',async (req,res) =>{
  try{
    var user_id = req.headers['user_id'];
    const myFav = await Favorites.find({ user_id: user_id });
    res.json(myFav);
  }catch(err){
    res.json({ message: err});
  }
});
//------------------------- get my favorite Movies id -------------------------\\ end
//------------------------- edit favorite start_send  -------------------------
app.put('/favorite/edit/start_send',async (req,res) =>{
  try {
    const updateFav = await Favorites.updateOne({ user_id: req.body.user_id, movie_id: req.body.movie_id },
      { $set: {
          start_send: true
      } });
    res.json(updateFav);
  } catch (err) {
    res.json({ message: err});
  }
});
//------------------------- edit favorite start_send  -------------------------\\ end
//------------------------- edit favorite end_send  -------------------------
app.put('/favorite/edit/end_send',async (req,res) =>{
  try {
    const updateFav = await Favorites.updateOne({ user_id: req.body.user_id, movie_id: req.body.movie_id },
      { $set: {
          end_send: true
      } });
    res.json(updateFav);
  } catch (err) {
    res.json({ message: err});
  }
});
//------------------------- edit favorite end_send  -------------------------\\ end
//------------------------- print all favorite Movies -------------------------
app.get('/movies/all/favorite',async (req,res) =>{
  var user_id = req.headers['user_id'];
  var anwser = [];
  try{
    const movies = await Movies.find();  // find all movies from db
    for(let curMovie of movies){
      const fav = await Favorites.findOne({ movie_id: curMovie._id , user_id: user_id  });
      var value_fav = 1;
      if(fav!=null){ // if this movies is favorite for this user
        const cinema = await Cinemas.findById(curMovie.cinema_id); // find cinema for current movie
        var singleMovie = {
          movie_id: curMovie._id,
          title: curMovie.title,
          start_date: curMovie.start_date,
          end_date: curMovie.end_date,
          category: curMovie.category,
          cinema: cinema.name,
          owner: cinema.owner_name,
          favorite: value_fav,
          start_send: fav.start_send,
          end_send: fav.end_send
        };
        anwser.push(singleMovie);
      }
    }
    res.json(anwser);
  }catch(err){
    res.json({ message: err});
  }
});
//------------------------- print all favorite Movies -------------------------\\ end

//------------------------- search Movies by Cinema Name -------------------------
app.get('/movies/cinemaName',async (req,res) =>{
  var user_id = req.headers['user_id'];
  var cinema_name = req.headers['cinema_name'];
  var anwser = [];
  try{
    var cinemasById = await Cinemas.find({ name: cinema_name });
    for(let curCinema of cinemasById){
      var movies = await Movies.find({ cinema_id: curCinema._id });
      for(let curMovie of movies){
        var fav = await Favorites.findOne({ movie_id: curMovie._id , user_id: user_id  });
        var value_fav = -1;
        if(fav==null){
          value_fav = 0;
        }
        else{
          value_fav = 1;
        }
        var singleMovie = {
          movie_id: curMovie._id,
          title: curMovie.title,
          start_date: curMovie.start_date,
          end_date: curMovie.end_date,
          category: curMovie.category,
          cinema: curCinema.name,
          owner: curCinema.owner_name,
          favorite: value_fav
        };
        anwser.push(singleMovie);
      }
    }
    res.json(anwser);
  }catch(err){
    res.json({ message: err});
  }
});
//------------------------- search Movies by Cinema Name -------------------------\\ end

//------------------------- search Movies by Category -------------------------
app.get('/movies/category',async (req,res) =>{
  var user_id = req.headers['user_id'];
  var category = req.headers['category'];
  var anwser = [];
  try{
    const movies = await Movies.find({ category: category });
    for(let curMovie of movies){
      const cinema = await Cinemas.findById(curMovie.cinema_id);
      const fav = await Favorites.findOne({ movie_id: curMovie._id , user_id: user_id  });
      var value_fav = -1;
      if(fav==null){
        value_fav = 0;
      }
      else{
        value_fav = 1;
      }
      var singleMovie = {
        movie_id: curMovie._id,
        title: curMovie.title,
        start_date: curMovie.start_date,
        end_date: curMovie.end_date,
        category: curMovie.category,
        cinema: cinema.name,
        owner: cinema.owner_name,
        favorite: value_fav
      };
      anwser.push(singleMovie);
    }
    res.json(anwser);
  }catch(err){
    res.json({ message: err});
  }
});
//------------------------- search Movies by Category -------------------------\\ end

//------------------------- search Movies by Title -------------------------
app.get('/movies/title',async (req,res) =>{
  var user_id = req.headers['user_id'];
  var title = req.headers['title'];
  console.log(user_id);
  console.log(title);
  var anwser = [];
  try{
    const movies = await Movies.find({ title: title });
    for(let curMovie of movies){
      const cinema = await Cinemas.findById(curMovie.cinema_id);
      const fav = await Favorites.findOne({ movie_id: curMovie._id , user_id: user_id  });
      var value_fav = -1;
      if(fav==null){
        value_fav = 0;
      }
      else{
        value_fav = 1;
      }
      var singleMovie = {
        movie_id: curMovie._id,
        title: curMovie.title,
        start_date: curMovie.start_date,
        end_date: curMovie.end_date,
        category: curMovie.category,
        cinema: cinema.name,
        owner: cinema.owner_name,
        favorite: value_fav
      };
      anwser.push(singleMovie);
    }
    res.json(anwser);
  }catch(err){
    res.json({ message: err});
  }
});
//------------------------- search Movies by Title -------------------------\\ end

//------------------------- search Movies by Date -------------------------
app.get('/movies/date',async (req,res) =>{
  var user_id = req.headers['user_id'];
  var date = req.headers['ddate'];
  var anwser = [];
  try{
    const movies = await Movies.find();
    const myDate = new Date(date);
    for(let curMovie of movies){
      if((myDate>curMovie.start_date && myDate<curMovie.end_date)||myDate==curMovie.start_date||myDate==curMovie.end_date){
        const cinema = await Cinemas.findById(curMovie.cinema_id);
        const fav = await Favorites.findOne({ movie_id: curMovie._id , user_id: user_id  });
        var value_fav = -1;
        if(fav==null){
          value_fav = 0;
        }
        else{
          value_fav = 1;
        }
        var singleMovie = {
          movie_id: curMovie._id,
          title: curMovie.title,
          start_date: curMovie.start_date,
          end_date: curMovie.end_date,
          category: curMovie.category,
          cinema: cinema.name,
          owner: cinema.owner_name,
          favorite: value_fav
        };
        anwser.push(singleMovie);
      }
    }
    res.json(anwser);
  }catch(err){
    res.json({ message: err});
  }
});
//------------------------- search Movies by Date -------------------------\\ end
//------------------------- get a Movie by Id -------------------------
app.get('/movies/get/id',async (req,res) =>{
  var id = req.headers['id'];
  try {
    const movie = await Movies.findOne({ _id: id });
    res.json(movie);
  } catch (err) {
    res.json({ message: err});
  }
});
//------------------------- get a Movie by Id -------------------------\\ end
//------------------------- delete Movie -------------------------
app.delete('/movies/delete',async (req,res) =>{
  try {
    const removeFav = await Favorites.remove({ movie_id: req.body.movie_id });
    const removeMovie = await Movies.remove({ _id: req.body.movie_id });
    res.json(removeMovie);
  } catch (err) {
    res.json({ message: err});
  }
});
//------------------------- delete Movie -------------------------\\ end
//------------------------- edit Movie -------------------------
app.put('/movies/edit',async (req,res) =>{
  console.log('inside edit');
  try {
    const updateMovie = await Movies.updateOne({ _id: req.body.movie_id },
      { $set: {
          title:req.body.title,
          start_date: new Date(req.body.start_date),
          end_date: new Date(req.body.end_date),
          category: req.body.category
      } });
    res.json(updateMovie);
  } catch (err) {
    res.json({ message: err});
  }
});
//------------------------- edit Movie -------------------------\\ end

//------------------------- print all Movies of a Cinema -------------------------
app.get('/movies/all-cinema',async (req,res) =>{
  var owner_id = req.headers['owner_id'];
  try{
    const myCinema = await Cinemas.findOne({ owner_id: owner_id });
    const movies = await Movies.find({ cinema_id: myCinema._id });
    res.json(movies);
  }catch(err){
    res.json({ message: err});
  }
});
//------------------------- print all Movies of a Cinema -------------------------\\ end
//------------------------- print all Movies by Category of a Cinema -------------------------
app.get('/movies/category-cinema',async (req,res) =>{
  var owner_id = req.headers['owner_id'];
  var category = req.headers['category'];
  try{
    const myCinema = await Cinemas.findOne({ owner_id: owner_id });
    const movies = await Movies.find({ cinema_id: myCinema._id, category: category });
    res.json(movies);
  }catch(err){
    res.json({ message: err});
  }
});
//------------------------- print all Movies by Category of a Cinema -------------------------\\ end
//------------------------- print all Movies by Title of a Cinema -------------------------
app.get('/movies/title-cinema',async (req,res) =>{
  var owner_id = req.headers['owner_id'];
  var title = req.headers['title'];
  try{
    const myCinema = await Cinemas.findOne({ owner_id: owner_id });
    const movies = await Movies.find({ cinema_id: myCinema._id, title: title });
    res.json(movies);
  }catch(err){
    res.json({ message: err});
  }
});
//------------------------- print all Movies by Title of a Cinema -------------------------\\ end
//------------------------- print all Movies by Date of a Cinema -------------------------
app.get('/movies/date-cinema',async (req,res) =>{
  var date = req.headers['ddate'];
  var owner_id = req.headers['owner_id'];
  var anwser = [];
  try{
    var myDate = new Date(date);
    const myCinema = await Cinemas.findOne({ owner_id: owner_id });
    const movies = await Movies.find({ cinema_id: myCinema._id });
    for(let curMovie of movies){
        if(myDate>curMovie.start_date && myDate<curMovie.end_date){
          anwser.push(curMovie);
        }else if(myDate==curMovie.start_date){
          anwser.push(curMovie);
        }else if(myDate==curMovie.end_date){
          anwser.push(curMovie);
        }
    }
    res.json(anwser);
  }catch(err){
    res.json({ message: err});
  }
});
//------------------------- print all Movies by Date of a Cinema -------------------------\\ end

var internet_db = 'mongodb+srv://konstas:123456kd@cluster0.hrrk3.mongodb.net/<dbname>?retryWrites=true&w=majority';
// Constants
const PORT = 8080;
const HOST = '172.18.1.8';
//Connect to database
mongoose
    .connect('mongodb://mongodb:27017',{
        useUnifiedTopology: true,
        useNewUrlParser: true,
        })
    .then(() => console.log('MongoDB Connected...'))
    .catch(err => console.log(err));

//How to we start listening to the server
app.listen(PORT, HOST);
console.log(`Running on http://${HOST}:${PORT}`);
