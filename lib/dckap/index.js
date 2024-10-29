/* import dependencies */
const fs = require('fs').promises
const puppeteer = require('puppeteer')
const express = require('express')
const cors = require('cors')

let port = process.env.PORT || 8000;


/* create express app */
const app = express()

/* apply middleware */
app.use(express.json())
app.use(cors())

/* path */
// const imageWritePath = 'E:/'
// const imageUrlPath = '/images'
// app.use(imageUrlPath, express.static(imageWritePath))

/* write image to path */
const writeImage = async (data, filename, path, fileExt) => {
    try {
      await fs.writeFile(`${path}/${filename}.${fileExt}`, data, {encoding: 'base64'});
    } 
    catch (error) {
      console.error(`Got an error trying to write to a image: ${error.message}`);
    }
}

/* routes */
app.post('/', async (req, res) => {
    let browser = null
    try {
        /* get data */
        const image = req.body

        /* set up browser and page */
        browser = await puppeteer.launch({
            headless: true,
            args: ['--no-sandbox', '--disable-setuid-sandbox'],
        })
        const page = await browser.newPage()
        page.setViewport({ width: 500, height: 500 })

        /* navigate to local html file to generate canvas element */
        await page.goto(`file://${__dirname}/artwork/index.html`)

        /* for console-log inside page.evaluate */
        page.on('console', msg => console.log(msg.text()))

        /* generate image */
        const imageUrl = await page.evaluate(async artworkData => {
            const {data, format} = artworkData
            const artwork = new Artwork({width: 500, height: 500}, document.body)
            await artwork.create(data)
            const render = artwork.render(format ? format : 'jpeg')
            return render
        }, image)

        /* close browser */
        await browser.close()

        /* response */
        res.send(imageUrl)
    } 
    catch (error) {
        /* close browser */
        browser && await browser.close()

        /* response */
        res.status(500).send(`Error occured while generating image: ${error}`)
    }
})

app.post('/artworks', async (req, res) => {
    let browser = null
    try {
        /* get data */
        const artworkData = req.body

        /* set up browser and page */
        browser = await puppeteer.launch({
            headless: true,
            args: ['--no-sandbox', '--disable-setuid-sandbox'],
        })
        const page = await browser.newPage()
        page.setViewport({ width: 500, height: 500 })

        /* navigate to local html file to generate canvas element */
        await page.goto(`file://${__dirname}/artwork/index.html`)

        /* for console-log inside page.evaluate */
        page.on('console', msg => console.log(msg.text()))

        console.log(req.body, "res BODY");

        /* generate image */
        const imageUrls = await page.evaluate(async artworkData => {
            const {data, format} = artworkData
            const urls = {}
            for(let id in data) {
                const artwork = new Artwork({width: 500, height: 500}, document.body)
                await artwork.create(data[id])
                urls[id] = artwork.render(format ? format : 'jpeg')
            }
            return urls
        }, artworkData)

        /* same image */
        for (let id in imageUrls) {
	        var d = new Date();
            var n = d.getTime();
	    
            let base64Image = imageUrls[id].replace(/^data:image\/\w+;base64,/, "")
            //await writeImage(base64Image, id+'_'+n, artworkData.path, artworkData.format)
            //imageUrls[id] = `${artworkData.url}${id}_${n}.${artworkData.format}`
            const currData =  artworkData['data'];
            const savedFileName = currData[id]['savedFileName'];
            await writeImage(base64Image, savedFileName, artworkData.path, artworkData.format)
            imageUrls[id] = `${artworkData.url}${savedFileName}.${artworkData.format}`
        }

        console.log("IMAGE URL ", imageUrls)

        /* close browser */
        await browser.close()



        /* response */
        res.send(imageUrls)
    } 
    catch (error) {
        /* close browser */
        browser && await browser.close()

        /* response */
        res.status(500).send(`Error occured while generating image: ${error}`)
    }
})


/* start express app */
app.listen(port, () => console.log('Running on port ' + port))
