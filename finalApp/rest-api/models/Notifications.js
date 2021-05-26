const mongoose = require('mongoose');

const NotificationsSchema = mongoose.Schema(
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
      category:{
        type: String,
        required: true
      },
      cinema_name:{
        type: String,
        required: true
      },
      movie_id:{
        type: String,
        required: true
      },
      users_id:[{ id: String }]
    }
);

module.exports = mongoose.model('Notifications',NotificationsSchema);
