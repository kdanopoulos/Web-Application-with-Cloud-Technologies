const mongoose = require('mongoose');

const MoviesSchema = mongoose.Schema(
    {
      title:{
        type: String,
        required: true
      },
      start_date:{
        type: Date,
        required: true
      },
      end_date:{
        type: Date,
        required: true
      },
      cinema_id:{
        type: String,
        required: true
      },
      category:{
        type: String,
        required: true
      }
    }
);

module.exports = mongoose.model('Movies',MoviesSchema);
