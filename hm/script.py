from flask import Flask, render_template, request, redirect
import mysql.connector

app = Flask(__name__)

# Connect to MySQL database
db = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="hm"
)

cursor = db.cursor()

# Define the function to get room services
def get_room_services(room_id):
    query = """
    SELECT rs.Service_ID, s.Service_Name, s.Cost
    FROM room_service rs
    INNER JOIN service s ON rs.Service_ID = s.Service_ID
    WHERE rs.Room_ID = %s
    """
    cursor.execute(query, (room_id,))
    return cursor.fetchall()

# Define the function to get room price
def get_room_price(room_id):
    cursor.execute("SELECT Price FROM room WHERE Room_ID = %s", (room_id,))
    return cursor.fetchone()[0]

# Define the function to check room availability
def is_room_available(room_id):
    cursor.execute("SELECT Availability_Status FROM room WHERE Room_ID = %s", (room_id,))
    status = cursor.fetchone()[0]
    return status

# Define the function to update room availability
def set_room_availability(room_id, availability):
    sql_query = f"UPDATE room SET Availability_Status = '{availability}' WHERE Room_ID = {room_id};"
    cursor.execute(sql_query)
    db.commit()

# Define the function to search for rooms based on hotel ID
def search_rooms(hotel_id):
    cursor.execute("SELECT Room_ID, Room_No, Availability_Status FROM room WHERE Hotel_ID = %s", (hotel_id,))
    return cursor.fetchall()

# Define the index route
@app.route('/', methods=['GET', 'POST'])
def index():
    rooms = []
    room_cost = 0
    service_cost = []
    total_cost = 0
    room_available = None
    f=open("room_status.txt","w")
    f.write("False")
    f.close()
    if request.method == 'POST':
        if 'room_search' in request.form:
            hotel_id = request.form['hotel']
            rooms = search_rooms(hotel_id)
        elif 'calculate_cost' in request.form:
            room_id = request.form['room']
            f=open("room_id.txt","w")
            f.write(room_id)
            f.close()
            room_cost = get_room_price(room_id)
            service_cost = get_room_services(room_id)
            total_cost = room_cost + sum(service[2] for service in service_cost)
            room_available = is_room_available(room_id)
        elif 'book_room' in request.form:
            f=open("room_id.txt","r")
            room_id=f.read()
            f.close()
            set_room_availability(room_id, 'Not Available')
            with open("room_status.txt", "w") as f:
                f.write("True")
            return redirect('/payment_confirmation')
    return render_template('index.html', rooms=rooms, room_cost=room_cost, service_cost=service_cost,
                           total_cost=total_cost, room_available=room_available)

@app.route('/payment_confirmation')
def payment_confirmation():
    with open("room_status.txt", "r") as f:
        room_booked = f.read().strip() == "True"
    if room_booked:
        return render_template('payment_confirmation.html')
    else:
        return redirect('/')
# Run the Flask application
if __name__ == '__main__':
    app.run(debug=True)
