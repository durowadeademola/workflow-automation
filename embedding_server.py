from flask import Flask, request, jsonify
from sentence_transformers import SentenceTransformer
import requests
import json
import os
from dotenv import load_dotenv

load_dotenv()

app = Flask(__name__)
model = SentenceTransformer('sentence-transformers/all-MiniLM-L6-v2')

SUPABASE_URL = os.getenv('SUPABASE_URL')
SUPABASE_KEY = os.getenv('SUPABASE_KEY')

@app.route('/fetch', methods=['POST'])
def fetch_page():
    data = request.json
    url = data.get('url', '')
    client_id = data.get('clientId', '')
    
    try:
        headers = {
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        }
        response = requests.get(url, headers=headers, timeout=15)
        return jsonify({
            'clientId': client_id,
            'url': url,
            'data': response.text,
            'success': True
        })
    except Exception as e:
        return jsonify({
            'clientId': client_id,
            'url': url,
            'data': '',
            'success': False,
            'error': str(e)
        })
    
@app.route('/embed', methods=['POST'])
def embed():
    data = request.json
    text = data.get('text', '')
    embedding = model.encode(text).tolist()
    
    # Build response with embedding
    response = { 'embedding': embedding }
    
    # Pass through ALL extra fields unchanged
    for key, value in data.items():
        if key != 'text':
            response[key] = value
    
    return jsonify(response)

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