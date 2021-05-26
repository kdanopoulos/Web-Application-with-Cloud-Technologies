const mongoose = require('mongoose');

const FavoritesSchema = mongoose.Schema(
    {
      user_id:{
        type: String,
        required: true
      },
      movie_id:{
        type: String,
        required: true
      },
      start_send:{
        type: Boolean,
        default: false
      },
      end_send:{
        type: Boolean,
        default: false
      }
    }
);

module.exports = mongoose.model('Favorites',FavoritesSchema);
