import evaluate_shop
import mysql.connector

# ✅ Connect to your database
conn = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",  # change if needed
    database="shop_locator"
)
cursor = conn.cursor()

# ✅ Get shops where approved is NULL
cursor.execute("SELECT shop_id FROM shops WHERE approved IS NULL")
shops = cursor.fetchall()

for (shop_id,) in shops:
    try:
        result = evaluate_shop.main(shop_id)

        # ✅ Update the shop with result (optional)
        cursor.execute("UPDATE shops SET approved = %s WHERE shop_id = %s", (result, shop_id))
        conn.commit()

        # ✅ Log the result
        with open("batch_log.txt", "a") as log:
            log.write(f"Shop {shop_id} → {result}\n")

    except Exception as e:
        # ✅ Log any error for that shop
        with open("batch_log.txt", "a") as log:
            log.write(f"Shop {shop_id} → ERROR: {str(e)}\n")

cursor.close()
conn.close()
