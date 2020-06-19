const express = require('express');
const bodyParser = require('body-parser');
const Pusher = require('pusher');
const pg = require('pg');

require('dotenv').config();

const app = express();

//let pgClient;

// Set View Engine
app.set('view engine', 'ejs');

// Set Connection Database
const pool = new pg.Pool({
    // Connect Satu
    user: 'eklanku_upayment',
    host: '10.101.11.51',
    database: 'eklanku_payment',
    password: '2%19!@eklanku',
    port: 5432,
    // Connect 2
    //connectionString: process.env.POSTGRES_CONNECTION_URL,
});

app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

// Connection Pusher
const pusher = new Pusher({
    appId: process.env.PUSHER_APP_ID,
    key: process.env.PUSHER_APP_KEY,
    secret: process.env.PUSHER_APP_SECRET,
    cluster: process.env.PUSHER_APP_CLUSTER,
    encrypted: true
});

// Connection PostgreSQL
pool.connect((err, client) => {
    if(err) {
        console.log(err);
      }
      pgClient = client;
      console.log("Success");
      client.on('notification', function(msg) {
        const payload = JSON.parse(msg.payload);
        // Check channel
        if(msg.channel === 'my_channel'){

            //console.log("ADD DATA TO antrian_transaksi >>>>> ", msg.payload);

            if (payload.transaksi_type == 'TRANSAKSI') {

                client.query("SELECT a.transaksi_id, a.mbr_code, to_char(a.tgl_trx, 'YYYY-MM-DD HH24:MI:SS' ) AS tgl_trx , to_char(a.tgl_sukses, 'YYYY-MM-DD HH24:MI:SS' ) AS tgl_sukses, a.harga_jual, a.tujuan, a.transaksi_status, a.receiver, a.product_kode, a.supliyer_id, a.transaksi_type, a.transaksi_jalur, b.mbr_name, d.supliyer_name, c.product_name FROM transaksi AS a JOIN mbr_list AS b ON a.mbr_code = b.mbr_code JOIN product AS c ON a.product_kode = c.product_kode JOIN supliyer AS d ON a.supliyer_id = d.supliyer_id WHERE a.mbr_code = '" + payload.mbr_code + "' AND a.product_kode = '" + payload.product_kode + "' AND a.supliyer_id = '" + payload.supliyer_id + "' ORDER BY a.tgl_trx DESC LIMIT 1").then( res => {
                    //client.release()
                    var array =  res.rows;
                    var objectjson = JSON.stringify(array[0]);
                    console.log(objectjson)
                    pusher.trigger('my_channel', 'my_event', JSON.parse(objectjson));
                })
                .catch(e => {
                    //client.release()
                    console.error('Query error', e.message, e.stack)
                })

            }

        } else if(msg.channel === 'my_channel_update') {


            if (payload.transaksi_type == 'TRANSAKSI') {

                client.query("SELECT a.transaksi_id, a.mbr_code, to_char(a.tgl_trx, 'YYYY-MM-DD HH24:MI:SS' ) AS tgl_trx , to_char(a.tgl_sukses, 'YYYY-MM-DD HH24:MI:SS' ) AS tgl_sukses, a.harga_jual, a.tujuan, a.transaksi_status, a.receiver, a.product_kode, a.supliyer_id, a.transaksi_type, a.transaksi_jalur, b.mbr_name, d.supliyer_name, c.product_name FROM transaksi AS a JOIN mbr_list AS b ON a.mbr_code = b.mbr_code JOIN product AS c ON a.product_kode = c.product_kode JOIN supliyer AS d ON a.supliyer_id = d.supliyer_id WHERE a.mbr_code = '" + payload.mbr_code + "' AND a.product_kode = '" + payload.product_kode + "' AND a.supliyer_id = '" + payload.supliyer_id + "' ORDER BY a.tgl_trx DESC LIMIT 1").then( res => {
                    //client.release()
                    var array =  res.rows;
                    var objectjson = JSON.stringify(array[0]);
                    console.log(objectjson)
                    pusher.trigger('my_channel_update', 'my_event_update', JSON.parse(objectjson));
                })
                .catch(e => {
                    //client.release()
                    console.error('Query error', e.message, e.stack)
                })

            }
        }

      });

      // Listen Postgre
      client.query('LISTEN my_channel');
      client.query('LISTEN my_channel_update');
});

// Run Server
app.listen(3000, () => {

    return console.log('Server is up on 3000')
});


