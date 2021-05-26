const mongoose = require('mongoose');

const CinemasSchema = mongoose.Schema(
    {
      owner_id:{
        type: String,
        required: true
      },
      owner_name:{
        type: String,
        required: true
      },
      name:{
        type: String,
        required: true
      }
    }
);

module.exports = mongoose.model('Cinemas',CinemasSchema);
