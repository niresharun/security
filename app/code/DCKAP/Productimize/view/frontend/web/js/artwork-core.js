const Artwork = function (dimension, parentElement) {
    /* get fabric objects with id */
    fabric.Canvas.prototype.getObjectsById = function (id) {
        let objectList = [], objects = this.getObjects()
        for (let i = 0; i < this.size(); i++) objects[i].id && objects[i].id === id && objectList.push(objects[i])
        return objectList
    }

    /* global variable */
    const canvasList = { artFabricCanvas: null, renderFabricCanvas: null }

    /* calculate ratio of d */
    const calculateRatio = (a, b, c) => c * b / a

    /* get image from url */
    const getImageByUrl = url => new Promise((resolve, reject) => {
        let img = new Image()
        img.crossOrigin = 'anonymous'
        img.addEventListener('load', () => resolve(img))
        img.addEventListener('error', (e) => reject(`Error occured while dowloading image from url: ${url}`))
        img.src = url
    })

    /* clone fabric object */
    const cloneFabricObject = fabricObject => new Promise((resolve, reject) => fabricObject.clone(clonedFabricObject => {
        !fabricObject && reject('invalied fabric object')
        clonedFabricObject.scaleX = fabricObject.scaleX
        clonedFabricObject.scaleY = fabricObject.scaleY
        resolve(clonedFabricObject)
    }, ['id', 'layer', 'sourceOrigin', 'objectLeft', 'evented', 'selectable']))

    /* calculate position index for objects */
    const getPositionIndex = (size, preIndex) => (size % 2 !== 0) ? (preIndex - 2) : (preIndex > 0) ? (preIndex) : (preIndex - 2)

    /* create artwork image */
    const createArtworkImage = params => new Promise(async (resolve, reject) => {
        try {
            const { image: { url }, boxDimension, fabricCanvas } = params
            const origin = fabricCanvas.width / 2

            /* create fabric image */
            const image = await getImageByUrl(url)
            const imageFabric = new fabric.Image(image, {
                id: 'artwork',
                left: origin,
                top: origin,
                angle: 0,
                opacity: 1,
                originX: 'center',
                originY: 'center',
                scaleX: 1,
                scaleY: 1,
                crossOrigin: 'anonymous',
                evented: true
            })

            /* calculate position of fabric image */
            const artworkWidth = fabricCanvas.width
            const artworkHeight = fabricCanvas.height
            const scale = imageFabric.width >= imageFabric.height ? calculateRatio(imageFabric.width, imageFabric.scaleX, artworkWidth) : calculateRatio(imageFabric.height, imageFabric.scaleY, artworkHeight)
            imageFabric.scaleX = scale
            imageFabric.scaleY = scale
            fabricCanvas.add(imageFabric)
            boxDimension.outerDimension.x = imageFabric.getBoundingRect(true, true).width
            boxDimension.outerDimension.y = imageFabric.getBoundingRect(true, true).height
            resolve({ imageFabric, boxDimension })
        }
        catch (error) {
            reject(error)
        }
    })

    /* draw artwork image with treatment */
    const createTreatmentImage = params => new Promise(async (resolve, reject) => {
        try {
            const { treatment: { url, width }, imageFabric, imageSizeInInch, calculatedBoxDimension, fabricCanvas } = params
            const origin = fabricCanvas.width / 2

            /* create fabric image */
            const treatmentImage = await getImageByUrl(url)
            const treatmentImageFabric = new fabric.Image(treatmentImage, {
                id: 'artwork',
                left: origin,
                top: origin,
                angle: 0,
                opacity: 1,
                originX: 'center',
                originY: 'center',
                scaleX: 1,
                scaleY: 1,
                crossOrigin: 'anonymous',
                evented: true
            })

            /* calculate treatment image, inch to scale */
            let treatmentImageSize = calculateRatio(imageSizeInInch.x, calculatedBoxDimension.outerDimension.x, width)
            let treatmentImageScale = calculateRatio(treatmentImageFabric.height, treatmentImageFabric.scaleY, treatmentImageSize)
            treatmentImageFabric.scaleX = treatmentImageScale
            treatmentImageFabric.scaleY = treatmentImageScale

            let countX = Math.ceil(imageFabric.getBoundingRect(true, true).width / treatmentImageFabric.getBoundingRect(true, true).width)
            let countY = Math.ceil(imageFabric.getBoundingRect(true, true).height / treatmentImageFabric.getBoundingRect(true, true).width)
            let count = { x: countX, y: countY }

            /* loop through sides - bottom, left, top, right */
            for (let side = 0, angle = 0; side < 4; side++, angle += 90) {
                let transformValue = [1, -1, -1, 1]
                let transfromParams = side % 2 === 0 ? ['left', 'top', 'width', 'height', 'x', 'y'] : ['top', 'left', 'height', 'width', 'y', 'x']
                let axis1 = count[transfromParams[4]]

                /* calculate position index for init object */
                let positionIndex = axis1 % 2 !== 0 ? ((axis1 - 1) / 2) : (axis1 / 2)

                /* create each side */
                for (let index = 0; index < axis1; index++) {
                    const treatmentObject = await cloneFabricObject(treatmentImageFabric)
                    treatmentObject.angle = angle
                    treatmentObject.setCoords()

                    /* create side images */
                    if (index === 0 && axis1 % 2 !== 0) {
                        treatmentObject[transfromParams[0]] = origin - (treatmentObject.getBoundingRect(true, true)[transfromParams[2]] * (index + positionIndex))
                        treatmentObject[transfromParams[1]] = origin + (transformValue[side] * imageFabric.getBoundingRect(true, true)[transfromParams[3]] / 2) - (transformValue[side] * treatmentObject.getBoundingRect(true, true)[transfromParams[3]] / 2)
                    }
                    else if (index === 0 && axis1 % 2 === 0) {
                        treatmentObject[transfromParams[0]] = origin - (treatmentObject.getBoundingRect(true, true)[transfromParams[2]] * positionIndex) + (treatmentObject.getBoundingRect(true, true)[transfromParams[2]] / 2)
                        treatmentObject[transfromParams[1]] = origin + (transformValue[side] * imageFabric.getBoundingRect(true, true)[transfromParams[3]] / 2) - (transformValue[side] * treatmentObject.getBoundingRect(true, true)[transfromParams[3]] / 2)
                    }
                    else if (axis1 % 2 !== 0) {
                        treatmentObject[transfromParams[0]] = origin - (treatmentObject.getBoundingRect(true, true)[transfromParams[2]] * (index + positionIndex))
                        treatmentObject[transfromParams[1]] = origin + (transformValue[side] * imageFabric.getBoundingRect(true, true)[transfromParams[3]] / 2) - (transformValue[side] * treatmentObject.getBoundingRect(true, true)[transfromParams[3]] / 2)
                    }
                    else if (axis1 % 2 === 0) {
                        treatmentObject[transfromParams[0]] = origin - (treatmentObject.getBoundingRect(true, true)[transfromParams[2]] * (index - positionIndex)) + (treatmentObject.getBoundingRect(true, true)[transfromParams[2]] / 2)
                        treatmentObject[transfromParams[1]] = origin + (transformValue[side] * imageFabric.getBoundingRect(true, true)[transfromParams[3]] / 2) - (transformValue[side] * treatmentObject.getBoundingRect(true, true)[transfromParams[3]] / 2)
                    }

                    /* add cloned object to canvas */
                    fabricCanvas.add(treatmentObject)

                    /* calculate position index for next object */
                    positionIndex = getPositionIndex(axis1, positionIndex)
                }
            }
            resolve(calculatedBoxDimension)
        } catch (error) {
            reject(error)
        }
    })

    /* draw artwork image */
    const createImage = params => new Promise(async (resolve, reject) => {
        try {
            const { image, treatment, boxDimension, fabricCanvas } = params
            let { imageFabric, boxDimension: calculatedBoxDimension } = await createArtworkImage({ image, boxDimension, fabricCanvas })
            if (treatment) calculatedBoxDimension = await createTreatmentImage({ treatment, imageFabric, imageSizeInInch: image.dimension, calculatedBoxDimension, fabricCanvas })
            fabricCanvas.requestRenderAll()
            resolve(calculatedBoxDimension)
        } catch (error) {
            reject(error)
        }
    })

    /* create mat for artwork */
    const createMat = params => new Promise(async (resolve, reject) => {
        try {
            const { data, imageSizeInInch, outerDimension, fabricCanvas } = params
            let { sideImage: imageUrl, width } = data
            let origin = { x: fabricCanvas.width / 2, y: fabricCanvas.height / 2 }
            width = typeof width === 'object' ? width : { bottom: parseFloat(width), left: parseFloat(width), top: parseFloat(width), right: parseFloat(width) }

            /* create fabric image */
            let image = await getImageByUrl(imageUrl)
            const imageFabric = new fabric.Image(image, {
                id: 'artwork',
                left: origin.x,
                top: origin.y,
                angle: 0,
                opacity: 1,
                originX: 'center',
                originY: 'center',
                scaleX: 1,
                scaleY: 1,
                crossOrigin: 'anonymous',
                evented: true
            })

            /* base image width */
            let { x: imageWidth, y: imageHeight } = outerDimension

            /* calculate width and height of image */
            for (let side in width) {
                if (side === 'top' || side === 'bottom') {
                    width[side] = calculateRatio(imageSizeInInch.x, outerDimension.x, width[side])
                    imageHeight += width[side]
                }
                else {
                    width[side] = calculateRatio(imageSizeInInch.x, outerDimension.x, width[side])
                    imageWidth += width[side]
                }
            }

            /* apply calculated width and height */
            imageFabric.scaleX = calculateRatio(imageFabric.width, imageFabric.scaleX, imageWidth)
            imageFabric.scaleY = calculateRatio(imageFabric.height, imageFabric.scaleY, imageHeight)

            /* calculate origin padding */
            let originPadding = { x: (width.left - width.right) / 2, y: (width.top - width.bottom) / 2 }

            /* calculate box dimension */
            let boxDimension = { outerDimension: { x: 0, y: 0 }, innerDimension: { x: 0, y: 0 } }
            boxDimension.outerDimension.x = imageFabric.getBoundingRect(true, true).width
            boxDimension.outerDimension.y = imageFabric.getBoundingRect(true, true).height
            boxDimension.innerDimension.x = outerDimension.x
            boxDimension.innerDimension.y = outerDimension.y

            /* scale image to fit inside canvas and re-calculate origin and box dimension */
            if (boxDimension.outerDimension.x >= boxDimension.outerDimension.y) {
                let tempOuterDimension = { ...boxDimension.outerDimension }
                boxDimension.outerDimension.y = calculateRatio(boxDimension.outerDimension.x, boxDimension.outerDimension.y, outerDimension.x)
                boxDimension.outerDimension.x = outerDimension.x

                boxDimension.innerDimension.x = calculateRatio(tempOuterDimension.x, boxDimension.innerDimension.x, boxDimension.outerDimension.x)
                boxDimension.innerDimension.y = calculateRatio(tempOuterDimension.y, boxDimension.innerDimension.y, boxDimension.outerDimension.y)

                originPadding.x = calculateRatio(tempOuterDimension.x, originPadding.x, boxDimension.outerDimension.x)
                originPadding.y = calculateRatio(tempOuterDimension.y, originPadding.y, boxDimension.outerDimension.y)
            }
            else if (boxDimension.outerDimension.y > boxDimension.outerDimension.x) {
                let tempOuterDimension = { ...boxDimension.outerDimension }
                boxDimension.outerDimension.x = calculateRatio(boxDimension.outerDimension.y, boxDimension.outerDimension.x, outerDimension.y)
                boxDimension.outerDimension.y = outerDimension.y

                boxDimension.innerDimension.x = calculateRatio(tempOuterDimension.x, boxDimension.innerDimension.x, boxDimension.outerDimension.x)
                boxDimension.innerDimension.y = calculateRatio(tempOuterDimension.y, boxDimension.innerDimension.y, boxDimension.outerDimension.y)

                originPadding.x = calculateRatio(tempOuterDimension.x, originPadding.x, boxDimension.outerDimension.x)
                originPadding.y = calculateRatio(tempOuterDimension.y, originPadding.y, boxDimension.outerDimension.y)
            }

            /* apply calculated width and height */
            imageFabric.scaleX = calculateRatio(imageFabric.getBoundingRect(true, true).width, imageFabric.scaleX, boxDimension.outerDimension.x)
            imageFabric.scaleY = calculateRatio(imageFabric.getBoundingRect(true, true).height, imageFabric.scaleY, boxDimension.outerDimension.y)

            /* add image to fabric canvas */
            fabricCanvas.add(imageFabric)
            fabricCanvas.requestRenderAll()

            resolve({ originPadding, boxDimension })
        }
        catch (error) {
            reject(error)
        }
    })

    /* create bricks around given dimension */
    const createBricks = params => new Promise(async (resolve, reject) => {
        try {
            const { data, imageSizeInInch, outerDimension, fabricCanvas } = params
            let { sideImage: sideImageUrl, cornerImage: cornerImageUrl, width } = data
            let bricks = {}
            let origin = { x: fabricCanvas.width / 2, y: fabricCanvas.height / 2 }
            width = typeof width === 'object' ? width : { bottom: parseFloat(width), left: parseFloat(width), top: parseFloat(width), right: parseFloat(width) }

            /* create bricks in inch */
            for (let side in width) {
                /* create side and corner fabric image */
                let sideImage = await getImageByUrl(sideImageUrl)
                const sideImageFabric = new fabric.Image(sideImage, {
                    id: 'artwork',
                    left: origin.x,
                    top: origin.y,
                    angle: 0,
                    opacity: 1,
                    originX: 'center',
                    originY: 'center',
                    scaleX: 1,
                    scaleY: 1,
                    crossOrigin: 'anonymous',
                    evented: true
                })
                let cornerImage = await getImageByUrl(cornerImageUrl ? cornerImageUrl : sideImageUrl)
                const cornerImageFabric = new fabric.Image(cornerImage, {
                    id: 'artwork',
                    left: origin.x,
                    top: origin.y,
                    angle: 0,
                    opacity: 1,
                    originX: 'center',
                    originY: 'center',
                    scaleX: 1,
                    scaleY: 1,
                    crossOrigin: 'anonymous',
                    evented: true
                })

                /* calculate side image, inch to scale */
                let sideImageSize = calculateRatio(imageSizeInInch.x, outerDimension.x, width[side])
                let imageScale = calculateRatio(sideImageFabric.height, sideImageFabric.scaleY, sideImageSize)
                sideImageFabric.scaleX = imageScale
                sideImageFabric.scaleY = imageScale
                cornerImageFabric.scaleX = imageScale
                cornerImageFabric.scaleY = imageScale

                /* calculate count for each width */
                let countX = Math.ceil(outerDimension.x / sideImageFabric.getBoundingRect(true, true).width)
                let countY = Math.ceil(outerDimension.y / sideImageFabric.getBoundingRect(true, true).width)
                let count = { x: countX, y: countY }
                bricks[side] = { sideImageFabric, cornerImageFabric, count }
            }

            /* calculate stretch value to fit bricks in dimension */
            for (let side in width) {
                if (side === 'bottom' || side === 'top') {
                    let innerDimensionX = bricks[side].sideImageFabric.getBoundingRect(true, true).width * bricks[side].count.x
                    let stretchValueInPx = (innerDimensionX - outerDimension.x) / bricks[side].count.x
                    let stretchValueInScale = calculateRatio(bricks[side].sideImageFabric.getBoundingRect(true, true).width, bricks[side].sideImageFabric.scaleX, stretchValueInPx)
                    bricks[side].sideImageFabric.scaleX -= stretchValueInScale
                }
                else {
                    let innerDimensionY = bricks[side].sideImageFabric.getBoundingRect(true, true).width * bricks[side].count.y
                    let stretchValueInPx = (innerDimensionY - outerDimension.y) / bricks[side].count.y
                    let stretchValueInScale = calculateRatio(bricks[side].sideImageFabric.getBoundingRect(true, true).width, bricks[side].sideImageFabric.scaleX, stretchValueInPx)
                    bricks[side].sideImageFabric.scaleX -= stretchValueInScale
                }
            }

            /* scale bricks to fit inside canvas */
            let rawDimensionX = (bricks.bottom.count.x * bricks.bottom.sideImageFabric.getBoundingRect(true, true).width) +
                bricks.left.sideImageFabric.getBoundingRect(true, true).height + bricks.right.sideImageFabric.getBoundingRect(true, true).height
            let rawDimensionY = (bricks.left.count.y * bricks.left.sideImageFabric.getBoundingRect(true, true).width) +
                bricks.top.sideImageFabric.getBoundingRect(true, true).height + bricks.bottom.sideImageFabric.getBoundingRect(true, true).height
            let dimension = { x: 0, y: 0 }
            if (rawDimensionX >= rawDimensionY) {
                let dimensionXScale = calculateRatio(rawDimensionX, bricks.bottom.sideImageFabric.scaleX, outerDimension.x)
                let dimensionYScale = calculateRatio(rawDimensionX, bricks.bottom.sideImageFabric.scaleY, outerDimension.x)
                let tempImage = await cloneFabricObject(bricks.bottom.sideImageFabric)
                tempImage.scaleX = dimensionXScale
                tempImage.scaleY = dimensionYScale
                tempImage.setCoords()
                let calculatedDimensionX = bricks.bottom.count.x * tempImage.getBoundingRect(true, true).width
                let calculatedDimensionY = calculateRatio(outerDimension.x, outerDimension.y, calculatedDimensionX)
                dimension = { x: calculatedDimensionX, y: calculatedDimensionY }
            }
            else if (rawDimensionX < rawDimensionY) {
                let dimensionXScale = calculateRatio(rawDimensionY, bricks.left.sideImageFabric.scaleX, outerDimension.y)
                let dimensionYScale = calculateRatio(rawDimensionY, bricks.left.sideImageFabric.scaleY, outerDimension.y)
                let tempImage = await cloneFabricObject(bricks.left.sideImageFabric)
                tempImage.scaleX = dimensionXScale
                tempImage.scaleY = dimensionYScale
                tempImage.setCoords()
                let calculatedDimensionY = bricks.left.count.y * tempImage.getBoundingRect(true, true).width
                let calculatedDimensionX = calculateRatio(outerDimension.y, outerDimension.x, calculatedDimensionY)
                dimension = { x: calculatedDimensionX, y: calculatedDimensionY }
            }
            for (let side in width) {
                if (side === 'bottom' || side === 'top') {
                    let totalDimension = bricks[side].count.x * bricks[side].sideImageFabric.getBoundingRect(true, true).width
                    let scaleX = calculateRatio(totalDimension, bricks[side].sideImageFabric.scaleX, dimension.x)
                    let scaleY = calculateRatio(totalDimension, bricks[side].sideImageFabric.scaleY, dimension.x)
                    bricks[side].sideImageFabric.scaleX = scaleX
                    bricks[side].sideImageFabric.scaleY = scaleY
                    bricks[side].cornerImageFabric.scaleX = scaleY
                    bricks[side].cornerImageFabric.scaleY = scaleY
                }
                else {
                    let totalDimension = bricks[side].count.y * bricks[side].sideImageFabric.getBoundingRect(true, true).width
                    let scaleX = calculateRatio(totalDimension, bricks[side].sideImageFabric.scaleX, dimension.y)
                    let scaleY = calculateRatio(totalDimension, bricks[side].sideImageFabric.scaleY, dimension.y)
                    bricks[side].sideImageFabric.scaleX = scaleX
                    bricks[side].sideImageFabric.scaleY = scaleY
                    bricks[side].cornerImageFabric.scaleX = scaleY
                    bricks[side].cornerImageFabric.scaleY = scaleY
                }
            }

            /* create corner image for end */
            bricks.bottom.cornerImageFabric1 = await cloneFabricObject(bricks.bottom.cornerImageFabric)
            bricks.top.cornerImageFabric1 = await cloneFabricObject(bricks.top.cornerImageFabric)
            bricks.left.cornerImageFabric1 = await cloneFabricObject(bricks.left.cornerImageFabric)
            bricks.right.cornerImageFabric1 = await cloneFabricObject(bricks.right.cornerImageFabric)

            /* resize corner image */
            bricks.bottom.cornerImageFabric.scaleX = calculateRatio(bricks.bottom.cornerImageFabric.getBoundingRect(true, true).width, bricks.bottom.cornerImageFabric.scaleX, bricks.left.cornerImageFabric.getBoundingRect(true, true).width)
            bricks.bottom.cornerImageFabric1.scaleX = calculateRatio(bricks.bottom.cornerImageFabric1.getBoundingRect(true, true).width, bricks.bottom.cornerImageFabric1.scaleX, bricks.right.cornerImageFabric.getBoundingRect(true, true).width)
            bricks.top.cornerImageFabric.scaleX = calculateRatio(bricks.top.cornerImageFabric.getBoundingRect(true, true).width, bricks.top.cornerImageFabric.scaleX, bricks.left.cornerImageFabric.getBoundingRect(true, true).width)
            bricks.top.cornerImageFabric1.scaleX = calculateRatio(bricks.top.cornerImageFabric1.getBoundingRect(true, true).width, bricks.top.cornerImageFabric1.scaleX, bricks.right.cornerImageFabric.getBoundingRect(true, true).width)
            bricks.left.cornerImageFabric.scaleX = calculateRatio(bricks.left.cornerImageFabric.getBoundingRect(true, true).height, bricks.left.cornerImageFabric.scaleX, bricks.top.cornerImageFabric.getBoundingRect(true, true).height)
            bricks.left.cornerImageFabric1.scaleX = calculateRatio(bricks.left.cornerImageFabric1.getBoundingRect(true, true).height, bricks.left.cornerImageFabric1.scaleX, bricks.bottom.cornerImageFabric.getBoundingRect(true, true).height)
            bricks.right.cornerImageFabric.scaleX = calculateRatio(bricks.right.cornerImageFabric.getBoundingRect(true, true).height, bricks.right.cornerImageFabric.scaleX, bricks.top.cornerImageFabric.getBoundingRect(true, true).height)
            bricks.right.cornerImageFabric1.scaleX = calculateRatio(bricks.right.cornerImageFabric1.getBoundingRect(true, true).height, bricks.right.cornerImageFabric1.scaleX, bricks.bottom.cornerImageFabric.getBoundingRect(true, true).height)

            /* find origin padding for bricks to position it around canvas centre */
            let outerDimensionXLeft = ((bricks.bottom.count.x * bricks.bottom.sideImageFabric.getBoundingRect(true, true).width) / 2) + bricks.left.sideImageFabric.getBoundingRect(true, true).height
            let outerDimensionXRight = ((bricks.bottom.count.x * bricks.bottom.sideImageFabric.getBoundingRect(true, true).width) / 2) + bricks.right.sideImageFabric.getBoundingRect(true, true).height
            let differenceX = outerDimensionXLeft - outerDimensionXRight
            let outerDimensionYTop = ((bricks.bottom.count.y * bricks.bottom.sideImageFabric.getBoundingRect(true, true).width) / 2) + bricks.top.sideImageFabric.getBoundingRect(true, true).height
            let outerDimensionYBottom = ((bricks.bottom.count.y * bricks.bottom.sideImageFabric.getBoundingRect(true, true).width) / 2) + bricks.bottom.sideImageFabric.getBoundingRect(true, true).height
            let differenceY = outerDimensionYTop - outerDimensionYBottom
            let originPadding = { x: differenceX / 2, y: differenceY / 2 }

            /* calculate outer dimension, inner dimension */
            let boxDimension = { outerDimension: { x: 0, y: 0 }, innerDimension: { x: 0, y: 0 } }
            boxDimension.innerDimension.x = bricks.bottom.sideImageFabric.getBoundingRect(true, true).width * bricks.bottom.count.x
            boxDimension.innerDimension.y = bricks.left.sideImageFabric.getBoundingRect(true, true).width * bricks.left.count.y
            boxDimension.outerDimension.x = boxDimension.innerDimension.x + bricks.left.sideImageFabric.getBoundingRect(true, true).height + bricks.right.sideImageFabric.getBoundingRect(true, true).height
            boxDimension.outerDimension.y = boxDimension.innerDimension.y + bricks.top.sideImageFabric.getBoundingRect(true, true).height + bricks.bottom.sideImageFabric.getBoundingRect(true, true).height

            /* loop through sides - bottom, left, top, right */
            for (let side = 0, angle = 0; side < 4; side++, angle += 90) {
                let sideParams = ['bottom', 'left', 'top', 'right']
                let count = bricks[sideParams[side]].count
                let { sideImageFabric, cornerImageFabric, cornerImageFabric1 } = bricks[sideParams[side]]
                let transformValue = [1, -1, -1, 1]
                let transfromParams = side % 2 === 0 ? ['left', 'top', 'width', 'height', 'x', 'y'] : ['top', 'left', 'height', 'width', 'y', 'x']
                let axis1 = count[transfromParams[4]]
                let cornerTransformParam = [
                    [{ angle: 0, flipX: false }, { angle: 0, flipX: true }],
                    [{ angle: 90, flipX: false }, { angle: 90, flipX: true }],
                    [{ angle: 0, flipY: true }, { angle: 180, flipY: false }],
                    [{ angle: 90, flipY: true }, { angle: -90, flipY: false }],
                ]

                /* calculate position index for init object */
                let positionIndex = axis1 % 2 !== 0 ? ((axis1 - 1) / 2) : (axis1 / 2)

                /* create each side */
                for (let index = 0; index < axis1; index++) {
                    /* clone fabric object */
                    const sideObject = await cloneFabricObject(sideImageFabric)
                    sideObject.angle = angle

                    /* create side images */
                    if (index === 0 && axis1 % 2 !== 0) {
                        sideObject[transfromParams[0]] = (origin[transfromParams[4]] + originPadding[transfromParams[4]]) - (sideObject.getBoundingRect(true, true)[transfromParams[2]] * (index + positionIndex))
                        sideObject[transfromParams[1]] = (origin[transfromParams[5]] + originPadding[transfromParams[5]]) + (transformValue[side] * boxDimension.innerDimension[transfromParams[5]] / 2) + (transformValue[side] * sideObject.getBoundingRect(true, true)[transfromParams[3]] / 2)
                    }
                    else if (index === 0 && axis1 % 2 === 0) {
                        sideObject[transfromParams[0]] = (origin[transfromParams[4]] + originPadding[transfromParams[4]]) - (sideObject.getBoundingRect(true, true)[transfromParams[2]] * positionIndex) + (sideObject.getBoundingRect(true, true)[transfromParams[2]] / 2)
                        sideObject[transfromParams[1]] = (origin[transfromParams[5]] + originPadding[transfromParams[5]]) + (transformValue[side] * boxDimension.innerDimension[transfromParams[5]] / 2) + (transformValue[side] * sideObject.getBoundingRect(true, true)[transfromParams[3]] / 2)
                    }
                    else if (axis1 % 2 !== 0) {
                        sideObject[transfromParams[0]] = (origin[transfromParams[4]] + originPadding[transfromParams[4]]) - (sideObject.getBoundingRect(true, true)[transfromParams[2]] * (index + positionIndex))
                        sideObject[transfromParams[1]] = (origin[transfromParams[5]] + originPadding[transfromParams[5]]) + (transformValue[side] * boxDimension.innerDimension[transfromParams[5]] / 2) + (transformValue[side] * sideObject.getBoundingRect(true, true)[transfromParams[3]] / 2)
                    }
                    else if (axis1 % 2 === 0) {
                        sideObject[transfromParams[0]] = (origin[transfromParams[4]] + originPadding[transfromParams[4]]) - (sideObject.getBoundingRect(true, true)[transfromParams[2]] * (index - positionIndex)) + (sideObject.getBoundingRect(true, true)[transfromParams[2]] / 2)
                        sideObject[transfromParams[1]] = (origin[transfromParams[5]] + originPadding[transfromParams[5]]) + (transformValue[side] * boxDimension.innerDimension[transfromParams[5]] / 2) + (transformValue[side] * sideObject.getBoundingRect(true, true)[transfromParams[3]] / 2)
                    }

                    /* create corner images */
                    if (index === 0) {
                        const cornerObject = await cloneFabricObject(cornerImageFabric)
                        cornerObject.setCoords()
                        cornerObject[transfromParams[0]] = (origin[transfromParams[4]] + originPadding[transfromParams[4]]) - (boxDimension.innerDimension[transfromParams[4]] / 2) - (cornerObject.getBoundingRect(true, true)[transfromParams[side % 2 === 0 ? 2 : 3]] / 2)
                        cornerObject[transfromParams[1]] = sideObject[transfromParams[1]]
                        cornerObject.set(cornerTransformParam[side][0])
                        fabricCanvas.add(cornerObject)
                    }
                    if (index === ((axis1 % 2 !== 0) ? (axis1 - 1) : 1)) {
                        const cornerObject = await cloneFabricObject(cornerImageFabric1)
                        cornerObject.setCoords()
                        cornerObject[transfromParams[0]] = (origin[transfromParams[4]] + originPadding[transfromParams[4]]) + (boxDimension.innerDimension[transfromParams[4]] / 2) + (cornerObject.getBoundingRect(true, true)[transfromParams[side % 2 === 0 ? 2 : 3]] / 2)
                        cornerObject[transfromParams[1]] = sideObject[transfromParams[1]]
                        cornerObject.set(cornerTransformParam[side][1])
                        fabricCanvas.add(cornerObject)
                    }

                    /* add cloned object to canvas */
                    fabricCanvas.add(sideObject)

                    /* calculate position index for next object */
                    positionIndex = getPositionIndex(axis1, positionIndex)
                }
            }
            fabricCanvas.requestRenderAll()
            resolve({ originPadding, boxDimension })
        } catch (error) {
            reject(error)
        }
    })

    /* get render image and cleanup canvas */
    const getRenderedImage = params => {
        const { fabricCanvas, renderedImages, boxDimension, originPadding, layer } = params
        let render = fabricCanvas.toDataURL({
            format: 'png',
            left: (fabricCanvas.width - boxDimension.outerDimension.x) / 2,
            top: (fabricCanvas.height - boxDimension.outerDimension.y) / 2,
            width: boxDimension.outerDimension.x,
            height: boxDimension.outerDimension.y
        })
        let renderImage = { image: render, dimension: boxDimension.innerDimension, originPadding, layer }
        renderedImages.push(renderImage)

        /* cleanup fabric canvas */
        fabricCanvas.remove(...fabricCanvas.getObjectsById('artwork'))
    }

    /* assemble rendered images */
    const assembleImage = (renderedImages, fabricCanvas) => new Promise(async (resolve, reject) => {
        try {
            for (let i = renderedImages.length - 1; i >= 0; i--) {
                /* add render image to canvas */
                const render = renderedImages[i]
                const image = await getImageByUrl(render.image)
                const origin = { x: fabricCanvas.width / 2, y: fabricCanvas.height / 2 }
                const imageFabric = new fabric.Image(image, {
                    id: 'artwork',
                    left: origin.x,
                    top: origin.y,
                    angle: 0,
                    opacity: 1,
                    originX: 'center',
                    originY: 'center',
                    scaleX: 1,
                    scaleY: 1,
                    layer: render.layer,
                    crossOrigin: 'anonymous',
                    evented: false
                })
                fabricCanvas.add(imageFabric)

                /* calculate render image position */
                if (renderedImages[i + 1] && renderedImages[i + 1].dimension) {
                    /* set tolerence value + 0.003 */
                    let scale = calculateRatio(imageFabric.width, imageFabric.scaleX, renderedImages[i + 1].dimension.x)
                    scale += i !== (renderedImages.length - 1) ? 0.003 : 0

                    const originPadding = renderedImages[i + 1].originPadding
                    if (originPadding) {
                        imageFabric.left = origin.x + originPadding.x
                        imageFabric.top = origin.y + originPadding.y
                    }
                    imageFabric.scaleX = scale
                    imageFabric.scaleY = scale
                    fabricCanvas.add(imageFabric)
                    if (render.dimension) {
                        render.dimension.x *= scale
                        render.dimension.y *= scale
                    }
                }

                /* calculate origin */
                if (render.originPadding) {
                    render.originPadding.x = calculateRatio(imageFabric.width, render.originPadding.x, imageFabric.getBoundingRect(true, true).width)
                    render.originPadding.y = calculateRatio(imageFabric.height, render.originPadding.y, imageFabric.getBoundingRect(true, true).height)
                }
                if (render.originPadding && renderedImages[i + 1] && renderedImages[i + 1].originPadding) {
                    render.originPadding.x += renderedImages[i + 1].originPadding.x
                    render.originPadding.y += renderedImages[i + 1].originPadding.y
                }
            }
            resolve()
        } catch (error) {
            reject(error)
        }
    })

    /* sort fabric object based on layer */
    const sortFabricObjects = canvas => canvas._objects.sort((objectA, objectB) => (parseInt(objectB.layer) || 0) - (parseInt(objectA.layer) || 0))

    /* create artwork canvas setup */
    const createArtworkCanvas = () => {
        /* create render canvas */
        const renderCanvasElement = document.createElement("canvas")
        renderCanvasElement.id = 'pz-canvas'
        parentElement.appendChild(renderCanvasElement)
        renderCanvasElement.setAttribute("width", `${dimension.width ? dimension.width : 1000}px`)
        renderCanvasElement.setAttribute("height", `${dimension.height ? dimension.height : 1000}px`)
        const renderFabricCanvas = new fabric.Canvas(renderCanvasElement, {
            enableRetinaScaling: false,
            preserveObjectStacking: true,
            selection: false,
            renderOnAddRemove: false
        })
        /* hide the render canvas once initiated */
        const canvasContainer = renderCanvasElement.parentElement
        canvasContainer.style.position = 'absolute'
        canvasContainer.style.visibility = 'hidden'
        canvasList.renderFabricCanvas = renderFabricCanvas

        /* create artwork canvas */
        const artCanvasElement = document.createElement("canvas")
        parentElement.appendChild(artCanvasElement)
        artCanvasElement.setAttribute("width", `${dimension.width ? dimension.width : 1000}px`)
        artCanvasElement.setAttribute("height", `${dimension.height ? dimension.height : 1000}px`)
        const artFabricCanvas = new fabric.Canvas(artCanvasElement, {
            enableRetinaScaling: false,
            preserveObjectStacking: true,
            selection: false,
            renderOnAddRemove: false
        })
        canvasList.artFabricCanvas = artFabricCanvas

        artFabricCanvas.backgroundColor = 'transparent'
    }

    /* get artwork image */
    this.render = imgFormat => {
        canvasList.artFabricCanvas.backgroundColor = imgFormat === 'jpeg' ? '#ffffff' : 'transparent'
        let image = canvasList.artFabricCanvas.toDataURL({ format: imgFormat ? imgFormat : 'jpeg', quality: 0.8 })
        canvasList.artFabricCanvas.backgroundColor = 'transparent'
        return image
    }

    /* cleanup canvas */
    this.cleanup = () => {
        for (let key in canvasList) {
            const lowerCanvasEl = canvasList[key].lowerCanvasEl
            canvasList[key].dispose()
            lowerCanvasEl.parentElement.removeChild(lowerCanvasEl)
        }
    }

    /* calculate watermark position */
    const calculateWatermarkTransform = (watermark, watermarkFabricImage, fabricCanvas) => {
        const { position, dimension } = watermark

        if (position === 'center') {
            return {
                position: { x: fabricCanvas.width / 2, y: fabricCanvas.height / 2 },
                scale: {
                    x: calculateRatio(watermarkFabricImage.getBoundingRect(true, true).width, watermarkFabricImage.scaleX, dimension.x),
                    y: calculateRatio(watermarkFabricImage.getBoundingRect(true, true).height, watermarkFabricImage.scaleY, dimension.y),
                }
            }
        }
        else if (position === 'stretch') {
            return {
                position: { x: fabricCanvas.width / 2, y: fabricCanvas.height / 2 },
                scale: {
                    x: calculateRatio(watermarkFabricImage.getBoundingRect(true, true).width, watermarkFabricImage.scaleX, fabricCanvas.width),
                    y: calculateRatio(watermarkFabricImage.getBoundingRect(true, true).height, watermarkFabricImage.scaleY, fabricCanvas.height),
                }
            }
        }
        else if (position === 'tile') {
            return {
                scale: {
                    x: calculateRatio(watermarkFabricImage.getBoundingRect(true, true).width, watermarkFabricImage.scaleX, dimension.x),
                    y: calculateRatio(watermarkFabricImage.getBoundingRect(true, true).height, watermarkFabricImage.scaleY, dimension.y),
                }
            }
        }
        else if (position === 'top-left') {
            const scale = {
                x: calculateRatio(watermarkFabricImage.getBoundingRect(true, true).width, watermarkFabricImage.scaleX, dimension.x),
                y: calculateRatio(watermarkFabricImage.getBoundingRect(true, true).height, watermarkFabricImage.scaleY, dimension.y),
            }
            const width = calculateRatio(watermarkFabricImage.scaleX, watermarkFabricImage.getBoundingRect(true, true).width, scale.x)
            const height = calculateRatio(watermarkFabricImage.scaleY, watermarkFabricImage.getBoundingRect(true, true).height, scale.y)
            return {
                position: {
                    x: width / 2,
                    y: height / 2,
                },
                scale
            }
        }
        else if (position === 'top-right') {
            const scale = {
                x: calculateRatio(watermarkFabricImage.getBoundingRect(true, true).width, watermarkFabricImage.scaleX, dimension.x),
                y: calculateRatio(watermarkFabricImage.getBoundingRect(true, true).height, watermarkFabricImage.scaleY, dimension.y),
            }
            const width = calculateRatio(watermarkFabricImage.scaleX, watermarkFabricImage.getBoundingRect(true, true).width, scale.x)
            const height = calculateRatio(watermarkFabricImage.scaleY, watermarkFabricImage.getBoundingRect(true, true).height, scale.y)
            return {
                position: {
                    x: fabricCanvas.width - width / 2,
                    y: height / 2,
                },
                scale
            }
        }
        else if (position === 'bottom-left') {
            const scale = {
                x: calculateRatio(watermarkFabricImage.getBoundingRect(true, true).width, watermarkFabricImage.scaleX, dimension.x),
                y: calculateRatio(watermarkFabricImage.getBoundingRect(true, true).height, watermarkFabricImage.scaleY, dimension.y),
            }
            const width = calculateRatio(watermarkFabricImage.scaleX, watermarkFabricImage.getBoundingRect(true, true).width, scale.x)
            const height = calculateRatio(watermarkFabricImage.scaleY, watermarkFabricImage.getBoundingRect(true, true).height, scale.y)
            return {
                position: {
                    x: width / 2,
                    y: fabricCanvas.height - height / 2,
                },
                scale
            }
        }
        else if (position === 'bottom-right') {
            const scale = {
                x: calculateRatio(watermarkFabricImage.getBoundingRect(true, true).width, watermarkFabricImage.scaleX, dimension.x),
                y: calculateRatio(watermarkFabricImage.getBoundingRect(true, true).height, watermarkFabricImage.scaleY, dimension.y),
            }
            const width = calculateRatio(watermarkFabricImage.scaleX, watermarkFabricImage.getBoundingRect(true, true).width, scale.x)
            const height = calculateRatio(watermarkFabricImage.scaleY, watermarkFabricImage.getBoundingRect(true, true).height, scale.y)
            return {
                position: {
                    x: fabricCanvas.width - width / 2,
                    y: fabricCanvas.height - height / 2,
                },
                scale
            }
        }
        return { position: null, scale: null }
    }

    /* add watermark to the artwork image */
    const createWatermark = (watermark, fabricCanvas) => new Promise(async (resolve, reject) => {
        try {
            const watermarkImage = await getImageByUrl(watermark.url)
            const watermarkFabricImage = new fabric.Image(watermarkImage, {
                id: 'artwork',
                opacity: watermark.opacity === 0 ? 0 : calculateRatio(100, 1, watermark.opacity),
                originX: 'center',
                originY: 'center',
                crossOrigin: 'anonymous',
                evented: false
            })

            /* add watermark to canvas */
            if (watermark.position !== 'tile') {
                const { position, scale } = calculateWatermarkTransform(watermark, watermarkFabricImage, fabricCanvas)
                watermarkFabricImage.set({
                    left: position && position.x,
                    top: position && position.y,
                    scaleX: scale && scale.x,
                    scaleY: scale && scale.y,
                })
                fabricCanvas.add(watermarkFabricImage)
            }
            else {
                const { scale } = calculateWatermarkTransform(watermark, watermarkFabricImage, fabricCanvas)
                watermarkFabricImage.set({
                    scaleX: scale && scale.x,
                    scaleY: scale && scale.y,
                })
                const count = { x: 0, y: 0 }
                count.x = (fabricCanvas.width / watermarkFabricImage.getBoundingRect(true, true).width) + 1
                count.y = (fabricCanvas.height / watermarkFabricImage.getBoundingRect(true, true).height) + 1

                for (let y = 0; y < count.y; y++) {
                    for (let x = 0; x < count.x; x++) {
                        const fabricObject = await cloneFabricObject(watermarkFabricImage)
                        fabricObject.set({
                            left: (x * watermarkFabricImage.getBoundingRect(true, true).width) + watermarkFabricImage.getBoundingRect(true, true).width / 2,
                            top: (y * watermarkFabricImage.getBoundingRect(true, true).height) + watermarkFabricImage.getBoundingRect(true, true).height / 2,
                        })
                        fabricCanvas.add(fabricObject)
                    }
                }
            }
            resolve()
        } catch (error) {
            reject(error)
        }
    })

    /* construct artwork */
    this.create = data => new Promise(async (resolve, reject) => {
        const { renderFabricCanvas, artFabricCanvas } = canvasList
        let { image, treatment, topMat, bottomMat, liner, frame, watermark } = JSON.parse(JSON.stringify(data))
        let boxDimension = { outerDimension: { x: 0, y: 0 }, innerDimension: { x: 0, y: 0 } }
        let renderedImages = []

        /* draw artwork */
        try {
            if (image && Object.keys(image).length !== 0) {
                /* draw artwork image */
                boxDimension = await createImage({ image, treatment, boxDimension, fabricCanvas: renderFabricCanvas })
                getRenderedImage({ fabricCanvas: renderFabricCanvas, renderedImages, boxDimension, layer: 1 })

                /* draw liner image */
                if (bottomMat && Object.keys(bottomMat).length !== 0) {
                    let result = await createMat({ data: bottomMat, imageSizeInInch: image.dimension, outerDimension: boxDimension.outerDimension, fabricCanvas: renderFabricCanvas })
                    boxDimension = result.boxDimension
                    getRenderedImage({ fabricCanvas: renderFabricCanvas, renderedImages, boxDimension, originPadding: result.originPadding, layer: 6 })
                }

                /* draw liner image */
                if (topMat && Object.keys(topMat).length !== 0) {
                    let result = await createMat({ data: topMat, imageSizeInInch: image.dimension, outerDimension: boxDimension.outerDimension, fabricCanvas: renderFabricCanvas })
                    boxDimension = result.boxDimension
                    getRenderedImage({ fabricCanvas: renderFabricCanvas, renderedImages, boxDimension, originPadding: result.originPadding, layer: 7 })
                }

                /* draw liner image */
                if (liner && Object.keys(liner).length !== 0) {
                    let result = await createBricks({ data: liner, imageSizeInInch: image.dimension, outerDimension: boxDimension.outerDimension, fabricCanvas: renderFabricCanvas })
                    boxDimension = result.boxDimension
                    getRenderedImage({ fabricCanvas: renderFabricCanvas, renderedImages, boxDimension, layer: 3 })
                }

                /* draw frame image */
                if (frame && Object.keys(frame).length !== 0) {
                    let result = await createBricks({ data: frame, imageSizeInInch: image.dimension, outerDimension: boxDimension.outerDimension, fabricCanvas: renderFabricCanvas })
                    boxDimension = result.boxDimension
                    getRenderedImage({ fabricCanvas: renderFabricCanvas, renderedImages, boxDimension, layer: 2 })
                }

                /* remove previous artwork */
                artFabricCanvas.remove(...artFabricCanvas.getObjectsById('artwork'))

                /* assemble rendered images inside canvas */
                await assembleImage(renderedImages, artFabricCanvas)

                /* sort fabric object based on layer */
                sortFabricObjects(artFabricCanvas)

                /* add watermark to the artwork image */
                watermark && await createWatermark(watermark, artFabricCanvas)

                /* render canvas */
                artFabricCanvas.requestRenderAll()

                resolve()
            }
            else
                reject('image data not found')
        }
        catch (error) {
            reject(error)
        }
    })

    /* create artwork canvas */
    createArtworkCanvas(dimension, parentElement)
}