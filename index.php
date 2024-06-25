<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCIU Student Search</title>
    <style>
        /* Reset default styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Header styles */
        h1 {
            text-align: center;
            margin-bottom: 10px;
        }

        /* Notice styles */
        #notice {
            text-align: center;
            margin-bottom: 20px;
            font-size: 14px;
            color: #555;
        }

        /* Form styles */
        form {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        select, input[type="text"] {
            padding: 10px;
            font-size: 16px;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="text"] {
            flex-grow: 1;
        }

        /* Suggestions container styles */
        #suggestionsContainer {
            width: 100%;
            display: flex;
            justify-content: center;
        }

        /* Suggestions table styles */
        #suggestionsTable {
            width: 100%;
            max-width: 800px;
            border-collapse: collapse;
            table-layout: fixed;
            margin: 0 auto; /* Center the table horizontally */
        }

        #suggestionsTable th, #suggestionsTable td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
            word-wrap: break-word;
        }

        #suggestionsTable th {
            background-color: #007bff;
            color: #fff;
        }

        /* Animation styles */
        @keyframes fadeIn {
            0% {
                opacity: 0;
            }
            100% {
                opacity: 1;
            }
        }

        #noResultsMessage {
            text-align: center;
            margin-top: 10px;
            display: none;
            animation: fadeIn 0.5s;
        }

        /* Developer text styles */
        #developer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #888;
        }

        /* Responsive styles */
        @media (max-width: 600px) {
            #suggestionsTable th, #suggestionsTable td {
                padding: 5px;
            }

            select, input[type="text"] {
                margin-bottom: 10px;
                flex-grow: 1;
                margin-right: 0;
            }
        }

        #developerNote {
            margin-top: 20px;
            text-align: center;
            font-size: 14px;
            color: #555;
        }

    </style>
</head>
<body>
    <h1>Student Search</h1>
    <div id="notice">This includes day batches from 20-32 only <br>[ Under processing and construction ]</div>
    <form method="GET" action="">
        <select id="table" name="table">
            <option value="CSE">CSE</option>
            <option value="EEE">EEE</option>
            <option value="BBA">BBA</option>
            <option value="CEN">CEN</option>
            <!-- Add more options for other tables here -->
        </select>
        <input type="text" name="query" id="query" placeholder="Search by student ID or name" oninput="getSuggestions(this.value)">
    </form>

    <div id="suggestionsContainer">
        <table id="suggestionsTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody id="suggestionsTableBody"><br><br><br><br><br><br><br>
            </tbody>
        </table>
    </div>
    <div id="noResultsMessage">No results found<br><br><br><br><br></div>
    
    <div id="developerNote">Searching by a partial name sometimes doesn't work. If that happens, try using a different part of the name, like the last name instead of the first name.</div>

    <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
    <div id="developer">Developed by 023 07224</div>

    <script>
        function getSuggestions(query) {
            const table = document.getElementById('table').value;
            if (query.trim() === '') {
                document.getElementById('suggestionsTable').style.display = 'none';
                document.getElementById('noResultsMessage').style.display = 'none';
                return;
            }
            fetch(`suggestions.php?query=${query}&table=${table}`)
                .then(response => response.json())
                .then(data => {
                    const tableBody = document.getElementById('suggestionsTableBody');
                    let html = '';
                    if (data.length === 0) {
                        document.getElementById('suggestionsTable').style.display = 'none';
                        document.getElementById('noResultsMessage').style.display = 'block';
                    } else {
                        data.forEach(item => {
                            html += `<tr>
                                        <td>${item.id}</td>
                                        <td>${item.name}</td>
                                        <td>Coming<br>Soon</td>
                                     </tr>`;
                        });
                        tableBody.innerHTML = html;
                        document.getElementById('suggestionsTable').style.display = 'table';
                        document.getElementById('noResultsMessage').style.display = 'none';
                    }
                });
        }
    </script>
</body>
</html>
