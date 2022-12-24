from selenium import webdriver
from PIL import Image
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
import time

url = 'https://www.nsctotal.com.br/noticias/sc-segue-com-alerta-para-deslizamentos-apesar-de-tregua-na-chuva'

#run first time to get scrollHeight
driver = webdriver.Chrome()
driver.get(url)
#pause 3 second to let page load
time.sleep(3)
#get scroll Height
height = driver.execute_script("return Math.max( document.body.scrollHeight, document.body.offsetHeight, document.documentElement.clientHeight, document.documentElement.scrollHeight, document.documentElement.offsetHeight )")
print(height)
#close browser
driver.close()

#Open another headless browser with height extracted above
chrome_options = Options()
chrome_options.add_argument("--headless")
chrome_options.add_argument(f"--window-size=1920,{height}")
chrome_options.add_argument("--hide-scrollbars")
chrome_options.add_argument('--no-sandbox')
chrome_options.add_argument('--disable-dev-shm-usage')
chrome_options.add_experimental_option("prefs", { "profile.default_content_setting_values.geolocation": 1})
driver = webdriver.Chrome("/usr/bin/chromedriver", options=chrome_options)
driver.get(url)

#pause 3 second to let page loads
time.sleep(3)
#save screenshot

driver.find_element("xpath", "//button[text()='Não, obrigado']").click()
driver.find_element(By.CLASS_NAME, "tp-close").click()

driver.save_screenshot('public/screen/shot.png')
driver.close()