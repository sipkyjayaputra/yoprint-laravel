<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.0.1/min/dropzone.min.css" rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.2.0/min/dropzone.min.js"></script>
        
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">


        <title>Laravel</title>
    </head>
    <body class="antialiased">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1></h1>
          
                    <form action="{{ route('dropzone.store') }}" method="post" enctype="multipart/form-data" id="image-upload" class="dropzone">
                        @csrf
                        <div>
                            <h3>Upload your file here!</h3>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="container mt-5">
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">Time</th>
                                <th scope="col">File Name</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        
        <script type="text/javascript">
            Dropzone.options = {
                maxFilesize         :       99999,
                acceptedFiles: ".csv"
            };
        </script>

        <script>
            const getDataMonitor = async () => {
                await fetch('http://localhost:8000/api/monitor-queue')
                    .then(response => response.json())
                    .then(data => {
                        const tableBody = document.getElementById("tableBody")
                        tableBody.innerHTML = "";
                        data.forEach(element => {
                            const tableRow = document.createElement("tr")
                            const timeRow = document.createElement("td")
                            const filenameRow = document.createElement("td")
                            const statusRow = document.createElement("td")
                            let timeData = document.createElement("span")
                            let filenameData = document.createElement("span")
                            let statusData = document.createElement("span")
                            timeData.innerText = element.time;
                            timeRow.appendChild(timeData);
                            filenameData.innerText = element.filename;
                            filenameRow.appendChild(filenameData);
                            statusData.innerText = element.status;
                            statusRow.appendChild(statusData);
                            tableRow.appendChild(timeRow)
                            tableRow.appendChild(filenameRow);
                            tableRow.appendChild(statusRow);
                            tableBody.appendChild(tableRow);
                            
                        });
                        setTimeout(getDataMonitor, 5000);
                    });
                
            }
            getDataMonitor();
        </script>
    </body>
</html>
