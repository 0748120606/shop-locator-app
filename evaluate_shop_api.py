from flask import Flask, request, jsonify
import evaluate_shop  # assumes you have evaluate_shop.py with a main(shop_id) function

app = Flask(__name__)

@app.route('/evaluate', methods=['POST'])
def evaluate():
    data = request.get_json()
    shop_id = int(data.get('shop_id'))
    result = evaluate_shop.main(shop_id)  # This runs your logic
    return jsonify({"status": result})

if __name__ == '__main__':
    app.run(port=5000)
