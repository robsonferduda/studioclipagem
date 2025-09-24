from apify_client import ApifyClient

# Initialize the ApifyClient with your Apify API token
# Replace '<YOUR_API_TOKEN>' with your token.
client = ApifyClient("apify_api_bh83rZnziLgttFItvGlJmfDTPVHxmk2HmYNb")

# Prepare the Actor input
run_input = {
    "hashtags": ["celesc"],
    "resultsType": "posts",
    "resultsLimit": 5,
}

# Run the Actor and wait for it to finish
run = client.actor("apify/instagram-hashtag-scraper").call(run_input=run_input)

# Fetch and print Actor results from the run's dataset (if there are any)
print("💾 Check your data here: https://console.apify.com/storage/datasets/" + run["defaultDatasetId"])
for item in client.dataset(run["defaultDatasetId"]).iterate_items():
    print(item)

# 📚 Want to learn more 📖? Go to → https://docs.apify.com/api/client/python/docs/quick-start