<?php

$sql = "SELECT * FROM devices WHERE active = 'Yes'";
$result = mysqli_query($conn, $sql);

?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Dashboard</h1>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-6">
            <div class="small-box bg-lightblue">
              <div class="inner">
                <h3><span id="suhu">0</span><sup style="font-size: 20px">O</sup> C</h3>

                <p>Suhu</p>
              </div>
              <div class="icon">
                <i class="fas fa-thermometer"></i>
              </div>
              </a>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="small-box bg-indigo">
              <div class="inner">
                <h3><span id="lembab">0</span><sup style="font-size: 20px"></sup> %</h3>

                <p>Kelembaban</p>
              </div>
              <div class="icon">
                <i class="fas fa-temperature-low"></i>
              </div>
              </a>
            </div>
          </div>

          <div class="col-lg-6">
            <!-- Radio Buttons -->
            <div class="card card-indigo">
              <div class="card-header">
                <h3 class="card-title">Alat Pakan Ayam</h3>
              </div>
              <div class="card-body table-responsive pad">
                <p class="mb-1">Alat A </p>
                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                  <label class="btn bg-olive active" id="label-pakan1-buka">
                    <input type="radio" name="pakan1" onchange="publishAlat(this)" id="pakan1-buka" autocomplete="off"> Buka
                  </label>
                  <label class="btn bg-olive" id="label-pakan1-tutup">
                    <input type="radio" name="pakan1" onchange="publishAlat(this)" id="pakan1-tutup" autocomplete="off"> Tutup
                  </label>
                </div>
              </div>
              <!-- /.card-body -->
            </div>
          </div>

          <div class="col-lg-6">
            <!-- Radio Buttons -->
            <div class="card card-info">
              <div class="card-header">
                <h3 class="card-title">Lampu Kandang</h3>
              </div>
              <div class="card-body table-responsive pad">
                <p class="mb-1">Lampu</p>
                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                  <label class="btn bg-olive active" id="label-lampu1-nyala">
                    <input type="radio" name="lampu1" onchange="publishLampu(this)" id="lampu1-nyala" autocomplete="off"> Nyala
                  </label>
                  <label class="btn bg-olive" id="label-lampu1-mati">
                    <input type="radio" name="lampu1"onchange="publishLampu(this)" id="lampu1-mati" autocomplete="off"> Mati
                  </label>
                </div>
              </div>
              <!-- /.card-body -->
            </div>
          </div>

          <div class="col-12">
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Status Perangkat</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0" style="height: 300px;">
                <table class="table table-head-fixed text-nowrap">
                  <thead>
                    <tr>
                      <th>Serial Number</th>
                      <th>Location</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>

                  <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                      <td><?php echo $row['serial_number']?></td>
                      <td><?php echo $row['location']?></td>
                      <td style="color:red;" id="kandang/status/<?php echo $row['serial_number'] ?>">offline</td>
                    </tr>
                  <?php } ?>

                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>

  <script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>

  <script>
    const clientId = Math.random().toString(16).substr(2, 8)
    const host = 'wss://bitterprincess291.cloud.shiftr.io:443';

    const options = {
      keepalive: 30,
      clientId: clientId,
      username: "bitterprincess291",
      password: "Kabere02",
      protocolId: 'MQTT',
      protocolVersion: 4,
      clean: true,
      reconnectPeriod: 1000,
      connectTimeout: 30 * 1000,
    };

    console.log("Menghubungkan ke Broker");
    const client = mqtt.connect(host, options);

    client.on("connect", ()=>{
      console.log("Terhubung");
      document.getElementById("status").innerHTML = "Terhubung";
      document.getElementById("status").style.color = "blue";

      client.subscribe("kandang/#", {qos: 1});
    });

    client.on("message", function(topic, payload){
      if(topic === "kandang/123456789/suhu"){
        document.getElementById("suhu").innerHTML = payload;
      } else if (topic === "kandang/123456789/lembab"){
        document.getElementById("lembab").innerHTML = payload;
      } else if(topic === "kandang/123456789/pakan"){
          if(payload == "buka"){
            document.getElementById("label-pakan1-buka").classList.add("active");
            document.getElementById("label-pakan1-tutup").classList.remove("active");
          } else {
            document.getElementById("label-pakan1-buka").classList.remove("active");
            document.getElementById("label-pakan1-tutup").classList.add("active");
          } 
      } else if(topic === "kandang/123456789/lampu"){
          if(payload == "nyala"){
            document.getElementById("label-lampu1-nyala").classList.add("active");
            document.getElementById("label-lampu1-mati").classList.remove("active");
          } else {
            document.getElementById("label-lampu1-nyala").classList.remove("active");
            document.getElementById("label-lampu1-mati").classList.add("active");
          }
        }

        if(topic.includes("kandang/status/")){
            document.getElementById(topic).innerHTML = payload;

            if(payload.toString() === "offline"){
                document.getElementById(topic).style.color = "red";
            } else if(payload.toString() === "online"){
                document.getElementById(topic).style.color = "blue";
            }
        }
    });


    function publishAlat(value){
      if (document.getElementById("pakan1-buka"). checked){
        data = "buka";
      }
      if (document.getElementById("pakan1-tutup"). checked){
        data = "tutup";
      }

      client.publish("kandang/123456789/pakan", data, {qos: 1, retain:true});
    }

    function publishLampu(value){
      if (document.getElementById("lampu1-nyala"). checked){
        data = "nyala";
      }
      if (document.getElementById("lampu1-mati"). checked){
        data = "mati";
      }

      client.publish("kandang/123456789/lampu", data, {qos: 1, retain:true});
    }
  </script>