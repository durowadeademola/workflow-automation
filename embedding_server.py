from flask import Flask, request, jsonify
from sentence_transformers import SentenceTransformer
import requests
import json
import os

app = Flask(__name__)
model = SentenceTransformer('sentence-transformers/all-MiniLM-L6-v2')

SUPABASE_URL = os.getenv('SUPABASE_URL')
SUPABASE_KEY = '' + os.getenv('SUPABASE_KEY')  # Ensure the key is prefixed with 'Bearer '

@app.route('/embed', methods=['POST'])
def embed():
    data = request.json
    text = data.get('text', '')
    embedding = model.encode(text).tolist()
    return jsonify({ 'embedding': embedding })

@app.route('/search', methods=['POST'])
def search():
    data = request.json
    text = data.get('text', '')
    client_id = data.get('client_id', '')
    threshold = data.get('threshold', 0.3)
    count = data.get('count', 5)

    # Generate embedding
    embedding = model.encode(text).tolist()

    # Search Supabase directly from Python
    response = requests.post(
        f'{SUPABASE_URL}/rest/v1/rpc/match_website_chunks',
        headers={
            'apikey': SUPABASE_KEY,
            'Authorization': f'Bearer {SUPABASE_KEY}',
            'Content-Type': 'application/json'
        },
        json={
            'query_embedding': embedding,
            'match_client_id': client_id,
            'match_threshold': threshold,
            'match_count': count
        }
    )

    results = response.json()
    return jsonify({
        'results': results,
        'chunks_found': len(results) if isinstance(results, list) else 0
    })

if __name__ == '__main__':
    app.run(port=5000)