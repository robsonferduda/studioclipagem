import requests

ACCESS_TOKEN = 'IGAARqxnNOaohBZAE9CUVhXTUhZANWlyTURoQUo5QjBGai1WMjBGOWNtZAGNLdlZAVdU04THM1MTc2T2VEWjRvZAUp2cS1YR3RKZAmZAYTkhyeGEySHhBZADlLb0lJLUhWNURmSnRBclNhdFYwdGV6SW5HQjRTYVZAEZA2xkMGNVRFNfYnMySQZDZD'
IG_USER_ID = '17841401430343493'  # ID da conta Instagram Business (obtido via /{page_id}?fields=instagram_business_account)

def buscar_id_hashtag(palavra_chave):
    url = f'https://graph.facebook.com/v19.0/ig_hashtag_search'
    params = {
        'user_id': IG_USER_ID,
        'q': palavra_chave,
        'access_token': ACCESS_TOKEN
    }
    res = requests.get(url, params=params)
    res.raise_for_status()
    data = res.json().get('data', [])
    if data:
        print(f"✅ Hashtag '{palavra_chave}' encontrada com ID {data[0]['id']}")
        return data[0]['id']
    else:
        raise Exception(f"❌ Hashtag '{palavra_chave}' não encontrada.")

def buscar_midias(hashtag_id):
    url = f'https://graph.facebook.com/v19.0/{hashtag_id}/recent_media'
    params = {
        'user_id': IG_USER_ID,
        'fields': 'id,caption,media_type,media_url,timestamp,permalink',
        'access_token': ACCESS_TOKEN
    }
    res = requests.get(url, params=params)
    res.raise_for_status()
    data = res.json().get('data', [])
    print(f"📦 {len(data)} mídias encontradas para essa hashtag.\n")
    for m in data:
        print(f"📅 {m['timestamp']}")
        print(f"📝 {m.get('caption', '(sem legenda)')[:80]}")
        print(f"🔗 {m['permalink']}\n")

# Uso
try:
    hashtag_id = buscar_id_hashtag('trilhas')
    buscar_midias(hashtag_id)
except Exception as e:
    print(str(e))

