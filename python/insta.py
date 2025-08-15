import os
import json
import requests

GRAPH_VER = 'v12.0'
IG_TOKEN = 'EAAICnmS4fO0BO5RV3GJqmbJ32WaNGdOmjUzZCtStd4BwakTx8jjcZA1RAHNO0sp6PikoyqZBSMYIA3L5aoc4KMz6RJFfqwYZA4n7FoEx5IsR3a7xmnYvpkGrATu34nmcSGoWdzydZAyRlaYADX3RLQBE2uRGGvWUacZA0j1gYw4jLfAZBJLtyogZBsvYDd6COTIZD'
IG_USER_ID = '17841408416359361'

def get(url, params):
    r = requests.get(url, params=params, timeout=20)
    if r.status_code != 200:
        try:
            err = r.json()
        except Exception:
            r.raise_for_status()
        print("Erro Graph:", json.dumps(err, ensure_ascii=False, indent=2))
        r.raise_for_status()
    return r.json()

def main():
    if not IG_TOKEN or not IG_USER_ID:
        print("Defina IG_TOKEN e IG_USER_ID.")
        return

    base = f'https://graph.facebook.com/{GRAPH_VER}'

    # Sanidade: id e username
    me = get(f'{base}/{IG_USER_ID}', {
        'fields': 'id,username',
        'access_token': IG_TOKEN
    })
    print("IG user:", me)

    # Mídias (campos mínimos, sem counts)
    data = get(f'{base}/{IG_USER_ID}/media', {
        'fields': 'id,caption,media_type,media_url,permalink,timestamp',
        'limit': 5,
        'access_token': IG_TOKEN
    })
    items = data.get('data', [])
    print(f"Midias retornadas: {len(items)}")
    for m in items:
        print("-", m.get('id'), m.get('media_type'), m.get('timestamp'),
              (m.get('caption') or '')[:80])
    print("Paging:", data.get('paging', {}))

if __name__ == '__main__':
    main()