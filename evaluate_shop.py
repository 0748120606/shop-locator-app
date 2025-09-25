import sys
import mysql.connector
import logging
from datetime import datetime
import difflib  # For fuzzy address/location matching

# Setup logging
logging.basicConfig(filename="evaluation_log.txt", level=logging.INFO,
                    format="%(asctime)s - %(message)s")

def is_similar_location(loc1, loc2, threshold=0.9):
    """Fuzzy match two locations using difflib."""
    return difflib.SequenceMatcher(None, loc1.lower(), loc2.lower()).ratio() >= threshold

def is_anomalous(shop, cursor):
    try:
        size = float(shop['size'])
        price = float(shop['price'])

        # Rule 1: Size must be > 10
        if size <= 10:
            return True

        # Rule 2: Price must be <= 250000
        if price > 250000:
            return True

        # Rule 3: Detect duplicates (same name, similar location, but different shop_id)
        cursor.execute("""
            SELECT shop_id, shop_name, location 
            FROM shops 
            WHERE shop_id != %s
        """, (shop['shop_id'],))
        existing_shops = cursor.fetchall()

        for other in existing_shops:
            if shop['shop_name'].strip().lower() == other['shop_name'].strip().lower():
                if is_similar_location(shop['location'], other['location']):
                    print(f"Duplicate found: {other['shop_name']} at {other['location']}")
                    return True  # Duplicate found

    except (TypeError, ValueError):
        return True  # Any invalid input is treated as anomalous

    return False

def log_decision(shop_id, result):
    logging.info(f"Shop {shop_id} - Result: {result}")

def evaluate_shop(shop_id):
    try:
        conn = mysql.connector.connect(
            host="localhost",
            user="root",
            password="",
            database="shop_locator"
        )
        cursor = conn.cursor(dictionary=True)

        cursor.execute("SELECT * FROM shops WHERE shop_id = %s", (shop_id,))
        shop = cursor.fetchone()

        if not shop:
            print(f"Shop ID {shop_id} not found.")
            return

        anomaly = is_anomalous(shop, cursor)
        decision = 0 if anomaly else None  # 0 = rejected, NULL = pending

        cursor.execute("UPDATE shops SET approved = %s WHERE shop_id = %s", (decision, shop_id))
        conn.commit()

        result_text = "REJECTED (Anomaly)" if anomaly else "PENDING"
        log_decision(shop_id, result_text)
        print(f"Shop {shop_id} - {result_text}")

    except mysql.connector.Error as err:
        print(f"Database error: {err}")
    finally:
        if 'conn' in locals() and conn.is_connected():
            cursor.close()
            conn.close()

if __name__ == "__main__":
    if len(sys.argv) > 1:
        try:
            shop_id = int(sys.argv[1])
            evaluate_shop(shop_id)
        except ValueError:
            print("Invalid shop_id. Please enter a numeric value.")
    else:
        print("Usage: python evaluate_shop.py <shop_id>")
